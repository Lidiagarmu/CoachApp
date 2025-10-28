import { Component, Input } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-navbar',
  templateUrl: './navbar.component.html'
})
export class NavbarComponent {
  @Input() showButtons: boolean = false;

  constructor(private router: Router) {}

  goHome() {
    this.router.navigate(['/dashboard']); // Ajusta según ruta
  }

  logout() {
    // Aquí tu lógica de logout
    localStorage.clear();
    this.router.navigate(['/login']);
  }
}