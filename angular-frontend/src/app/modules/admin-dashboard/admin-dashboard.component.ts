import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NavbarComponent } from '../../shared/components/navbar/navbar.component';
import { AuthService } from '../../services/auth.service';

import { MenuToggleComponent } from '../../shared/components/menu-toggle/menu-toggle.component';
import { LogoutButtonComponent } from '../../shared/components/logout-button/logout-button.component';


@Component({
  selector: 'app-admin-dashboard',
  standalone: true,
  imports: [CommonModule, NavbarComponent, MenuToggleComponent, LogoutButtonComponent],
  templateUrl: './admin-dashboard.component.html',
})
export class AdminDashboardComponent implements OnInit {
  nickname: string | null = null;
  fullName: string = '';
  role: string = 'Administrador';
  profilePhoto: string | null = null;

  activeTab: 'players' | 'coaches' | 'teams' | 'settings' = 'players';

  isMenuOpen: boolean = false;

  toggleMenu() {
    this.isMenuOpen = !this.isMenuOpen;
  }


  constructor(private authService: AuthService) {}

  ngOnInit(): void {
    this.authService.fetchUserFromApi().subscribe({
      next: user => {
        this.nickname = user.nickname || '';
        this.fullName = user.fullName || '';
        this.role = user.roles?.includes('ROLE_ADMIN') ? 'Administrador' :
          user.roles?.includes('ROLE_COACH') ? 'Entrenador' :
          'Jugador';
      },
      error: () => {
        // Opcional: manejar error, logout si token inválido
        this.authService.logout();
      }
    });
  }

  openTab(tab: 'players' | 'coaches' | 'teams' | 'settings'): void {
    this.activeTab = tab;
  }
}
