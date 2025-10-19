import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './register.component.html',
})
export class RegisterComponent {
  email = '';
  password = '';
  fullName = '';
  nickname = '';
  age: number | null = null;
  type: 'coach' | 'player' = 'player';

  constructor(private auth: AuthService, private router: Router) {}

  register() {
    this.auth.register({
      email: this.email,
      password: this.password,
      fullName: this.fullName,
      nickname: this.nickname,
      age: this.age ?? undefined,
      type: this.type
    }).subscribe({
      next: res => this.router.navigate(['/login']),
      error: err => alert(err.error?.message || 'Register failed')
    });
  }
}
