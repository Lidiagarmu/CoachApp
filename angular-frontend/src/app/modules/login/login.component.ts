import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { NavbarComponent } from '../../shared/components/navbar/navbar.component';
import { BackButtonComponent } from '../../shared/components/back-button/back-button.component';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterModule, NavbarComponent, BackButtonComponent],
  templateUrl: './login.component.html'
})

export class LoginComponent {
  loginForm: FormGroup;
  errorMessage: string | null = null;
  showPassword = false; // ✅ Añadido para toggle del ojo

  constructor(private fb: FormBuilder, private auth: AuthService, private router: Router) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', Validators.required]
    });
  }

  login() {
    if (this.loginForm.invalid) {
      this.errorMessage = 'Completa todos los campos correctamente';
      return;
    }

    const { email, password } = this.loginForm.value;

    this.auth.login(email, password).subscribe({
      next: () => {
        const roles = this.auth.getUser()?.roles || [];
        if (roles.includes('ROLE_COACH')) this.router.navigate(['/coach']);
        else if (roles.includes('ROLE_PLAYER')) this.router.navigate(['/player']);
        else if (roles.includes('ROLE_ADMIN')) this.router.navigate(['/admin']);
      },
      error: (err) => {
        console.error('Login error', err);
        this.errorMessage = err.error?.message || 'Usuario o contraseña incorrectos';
      }
    });
  }
}
