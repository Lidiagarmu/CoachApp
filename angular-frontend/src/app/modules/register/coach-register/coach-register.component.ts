import { Component, OnInit } from '@angular/core';
import { FormBuilder, Validators, FormGroup } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../../services/auth.service';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../../environments/enviroment';
import { CommonModule } from '@angular/common';
import { NavbarComponent } from '../../../shared/components/navbar/navbar.component';
import { BackButtonComponent } from '../../../shared/components/back-button/back-button.component';
import { ReactiveFormsModule } from '@angular/forms'; 

@Component({
  selector: 'app-coach-register',
  templateUrl: './coach-register.component.html',
  imports: [CommonModule, ReactiveFormsModule, NavbarComponent, BackButtonComponent],
  standalone: true
})
export class CoachRegisterComponent implements OnInit {
  showPassword = false;
  showRepeatPassword = false;
  passwordStrength = 0;
  errorMessage: string | null = null;
  teams: any[] = [];
  registerForm!: FormGroup;

  constructor(private fb: FormBuilder, private auth: AuthService, private http: HttpClient, private router: Router) {
    this.registerForm = this.fb.group({
      fullName: ['', Validators.required],
      nickname: [''],
      age: [null],
      teamName: [''],
      createLater: [false],
      yearsExperience: [0],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
      repeatPassword: ['', [Validators.required]]
    });
  }

  ngOnInit(): void {
    this.http.get<any[]>(`${environment.apiUrl}/teams`).subscribe({
      next: res => this.teams = res,
      error: err => console.error('Error cargando equipos', err)
    });
  }

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
      type: 'coach'
    };

    this.auth.register(data).subscribe({
      next: () => this.router.navigate(['/login']),
      error: err => this.errorMessage = err.error?.message || 'Error al registrarse'
    });
  }
}
