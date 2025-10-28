import { Component, Input, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule, NgIf } from '@angular/common';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [CommonModule, NgIf],
  templateUrl: './navbar.component.html',
})
export class NavbarComponent implements OnInit {

  @Input() showButton: boolean = false;

  constructor(private router: Router) {}

  ngOnInit(): void {
    console.log('✅ Navbar inicializado, showButton =', this.showButton);
  }

  logout() {
    // 🧹 Limpieza general de datos de sesión
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    localStorage.removeItem('role');

    // 🔁 Redirección al login
    this.router.navigate(['/login']);
  }
}
