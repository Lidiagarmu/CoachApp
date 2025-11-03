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
  registerForm!: FormGroup;
  teams: any[] = [];
  showPassword = false;
  showRepeatPassword = false;
  errorMessage: string | null = null;

  // Estado de requisitos de contraseña
  passwordRequirements = {
    minLength: false,
    uppercase: false,
    lowercase: false,
    number: false,
    special: false
  };

  constructor(
    private fb: FormBuilder,
    private auth: AuthService,
    private http: HttpClient,
    private router: Router
  ) {
    this.registerForm = this.fb.group(
      {
        fullName: ['', [Validators.required, Validators.minLength(3)]],
        nickname: [''],
        age: [null, [Validators.min(18), Validators.max(99)]],
        teamName: [''],
        createLater: [false],
        yearsExperience: [0, [Validators.min(0), Validators.max(60)]],
        email: ['', [Validators.required, Validators.email]],
        password: ['', [Validators.required, Validators.minLength(6)]],
        repeatPassword: ['', [Validators.required]]
      },
      { validators: this.passwordsMatchValidator }
    );
  }

  ngOnInit(): void {
    // ✅ Cargar equipos
    this.http.get<any[]>(`${environment.apiUrl}/teams`).subscribe({
      next: (res) => (this.teams = res),
      error: (err) => console.error('Error cargando equipos', err)
    });

    // ✅ Escuchar cambios del checkbox “crear más tarde”
    this.registerForm.get('createLater')?.valueChanges.subscribe((checked) => {
      const teamNameControl = this.registerForm.get('teamName');
      if (checked) {
        teamNameControl?.disable(); // Desactiva el input
        teamNameControl?.reset();   // Limpia su valor
      } else {
        teamNameControl?.enable();  // Reactiva si se desmarca
      }
    });
  }

  // ✅ Verifica que ambas contraseñas coincidan
  private passwordsMatchValidator(formGroup: FormGroup) {
    const password = formGroup.get('password')?.value;
    const repeatPassword = formGroup.get('repeatPassword')?.value;
    return password === repeatPassword ? null : { passwordMismatch: true };
  }

  // ✅ Actualiza la validación de los requisitos de contraseña
  onPasswordChange(value: string) {
    this.passwordRequirements = {
      minLength: value.length >= 6,
      uppercase: /[A-Z]/.test(value),
      lowercase: /[a-z]/.test(value),
      number: /[0-9]/.test(value),
      special: /[\W_]/.test(value)
    };
  }

  // ✅ Registro
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
      error: (err) => {
        this.errorMessage = err.error?.error || 'Error al registrarse';
      }
    });
  }
}
