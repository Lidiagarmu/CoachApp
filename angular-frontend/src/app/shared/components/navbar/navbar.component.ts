import { Component, Input, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule, NgIf } from '@angular/common';

import { AuthService } from '../../../services/auth.service';


@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [CommonModule, NgIf],
  templateUrl: './navbar.component.html',
})
export class NavbarComponent implements OnInit {

  @Input() showButton: boolean = false;

  constructor(
    private router: Router,
    private authService: AuthService
  ) {}

  ngOnInit(): void {
    console.log('✅ Navbar inicializado, showButton =', this.showButton);
  }

  logout() {
    this.authService.logout();          // usa método centralizado
    this.router.navigate(['/login']);    // redirige al login
  }
}
