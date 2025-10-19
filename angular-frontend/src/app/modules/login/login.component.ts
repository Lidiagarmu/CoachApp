import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './login.component.html',
})
export class LoginComponent {
  email = '';
  password = '';

  constructor(private auth: AuthService, private router: Router) {}

  login() {
    this.auth.login(this.email, this.password).subscribe({
      next: res => {
        const roles = res.user?.roles || [];
        if (roles.includes('ROLE_COACH')) this.router.navigate(['/coach']);
        else if (roles.includes('ROLE_PLAYER')) this.router.navigate(['/player']);
        else if (roles.includes('ROLE_ADMIN')) this.router.navigate(['/admin']);
      },
      error: err => alert(err.error?.message || 'Login failed')
    });
  }
}
