import { Component, HostListener } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { NavbarComponent } from '../../shared/components/navbar/navbar.component';
import { BackButtonComponent } from '../../shared/components/back-button/back-button.component';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterModule, NavbarComponent, BackButtonComponent],
  templateUrl: './register.component.html'
})

export class RegisterComponent {
  registerForm: FormGroup;
  errorMessage: string | null = null;
  showPassword = false; // ✅ Toggle para la contraseña

  isDropdownOpen = false;
  selectedType: string | null = null;

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
    if (data.age === null) delete data.age;

    this.auth.register(data).subscribe({
      next: () => this.router.navigate(['/login']),
      error: (err) => {
        console.error('Register error', err);
        this.errorMessage = err.error?.message || 'Error al registrarse';
      }
    });
  }


  // función para desplegable en el input seleccionar el tipo de usuario
  selectType(type: string) {
    this.selectedType = type;
    this.registerForm.get('type')?.setValue(type);
    this.isDropdownOpen = false;
  }

   // cierra el dropdown al hacer clic fuera
  @HostListener('document:click', ['$event'])
  onClickOutside(event: MouseEvent) {
    const target = event.target as HTMLElement;
    // Si el clic no ocurrió dentro del dropdown ni en el botón que lo abre
    if (!target.closest('.dropdown-wrapper')) {
      this.isDropdownOpen = false;
    }
  }

}
