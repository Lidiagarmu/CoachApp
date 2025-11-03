import { Component, OnInit } from '@angular/core';
import { FormBuilder, Validators, ReactiveFormsModule, FormGroup } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../../services/auth.service';
import { CommonModule } from '@angular/common';
import { NavbarComponent } from '../../../shared/components/navbar/navbar.component';
import { BackButtonComponent } from '../../../shared/components/back-button/back-button.component';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../../environments/enviroment';

@Component({
  selector: 'app-player-register',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, NavbarComponent, BackButtonComponent],
  templateUrl: './player-register.component.html'
})

export class PlayerRegisterComponent implements OnInit {
  showPassword = false;
  showRepeatPassword = false;
  passwordStrength = 0; // actualizar con tu lógica
  errorMessage: string | null = null;
  teams: any[] = [];
  registerForm!: FormGroup;

  constructor(private fb: FormBuilder, private auth: AuthService, private http: HttpClient, private router: Router) {
    this.registerForm = this.fb.group({
      fullName: ['', Validators.required],
      nickname: [''],
      age: [null],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
      repeatPassword: ['', [Validators.required]],
      position: [''],
      number: [0],
      teamId: [null]
    });
  }

  ngOnInit(): void {
    this.http.get<any[]>(`${environment.apiUrl}/teams`).subscribe({
      next: res => this.teams = res,
      error: err => console.error('Error cargando equipos', err)
    });
  }

  // Aquí puedes actualizar la barra de progreso según la contraseña
  updatePasswordStrength() {
    const value = this.registerForm.get('password')?.value || '';
    let strength = 0;
    if (/[A-Z]/.test(value)) strength += 25;
    if (/[a-z]/.test(value)) strength += 25;
    if (/[0-9]/.test(value)) strength += 25;
    if (/[\W_]/.test(value)) strength += 25;
    this.passwordStrength = strength;
  }

  register() {
    if (this.registerForm.invalid) {
      this.errorMessage = 'Completa todos los campos correctamente.';
      return;
    }

    const data = {
      ...this.registerForm.getRawValue(),
      type: 'player'
    };

    this.auth.register(data).subscribe({
      next: () => this.router.navigate(['/login']),
      error: err => this.errorMessage = err.error?.message || 'Error al registrarse'
    });
  }
}

