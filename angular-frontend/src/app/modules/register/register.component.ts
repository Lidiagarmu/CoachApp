import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterModule],
  templateUrl: './register.component.html'
})
export class RegisterComponent {
  registerForm: FormGroup;
  errorMessage: string | null = null;

  constructor(private fb: FormBuilder, private auth: AuthService, private router: Router) {
    this.registerForm = this.fb.group({
      fullName: ['', Validators.required],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
      nickname: [''],
      age: [null],
      type: ['player', Validators.required]
    });
  }

  register() {
    if (this.registerForm.invalid) {
      this.errorMessage = 'Completa todos los campos correctamente';
      return;
    }

    const data = { ...this.registerForm.value };
    // Convertir age null a undefined
    if (data.age === null) delete data.age;

    this.auth.register(data).subscribe({
      next: () => this.router.navigate(['/login']),
      error: (err) => {
        console.error('Register error', err);
        this.errorMessage = err.error?.message || 'Error al registrarse';
      }
    });
  }
}
