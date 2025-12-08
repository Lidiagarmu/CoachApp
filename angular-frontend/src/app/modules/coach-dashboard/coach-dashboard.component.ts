import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NavbarComponent } from '../../shared/components/navbar/navbar.component';
import { AuthService } from '../../services/auth.service';
import { CoachTeamComponent } from './coach-team/coach-team.component';
import { CoachPlayersComponent } from './coach-players/coach-players.component';
import { CoachEventsComponent } from './coach-events/coach-events.component';
import { MenuToggleComponent } from '../../shared/components/menu-toggle/menu-toggle.component';
import { LogoutButtonComponent } from '../../shared/components/logout-button/logout-button.component';

@Component({
  selector: 'app-coach-dashboard',
  standalone: true,
  imports: [
    CommonModule,
    NavbarComponent,
    CoachTeamComponent,
    CoachPlayersComponent,
    CoachEventsComponent,
    MenuToggleComponent,
    LogoutButtonComponent
  ],
  templateUrl: './coach-dashboard.component.html',
})
export class CoachDashboardComponent implements OnInit {
  nickname: string | null = null;
  fullName: string = '';
  role: string = 'Entrenador';

  // 🔹 Controla la pestaña activa
  activeTab: 'team' | 'players' | 'events' | 'settings' = 'team';

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
      this.role = user.roles?.includes('ROLE_COACH') ? 'Entrenador' : 'Jugador';
    },
    error: () => {
      // Opcional: manejar error, logout si token inválido
      this.authService.logout();
    }
  });
}




  // 🔹 Navegación entre secciones
  openTab(tab: 'team' | 'players' | 'events' | 'settings'): void {
    this.activeTab = tab;
  }
}
