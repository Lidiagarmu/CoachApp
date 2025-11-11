import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NavbarComponent } from '../../shared/components/navbar/navbar.component';
import { AuthService } from '../../services/auth.service';
import { TeamInvitationService} from '../../services/team-invitation.service';
import { PlayerTeamComponent } from './player-team/player-team.component';

@Component({
  selector: 'app-player-dashboard',
  standalone: true,
  imports: [CommonModule, NavbarComponent, PlayerTeamComponent],
  templateUrl: './player-dashboard.component.html',
})
export class PlayerDashboardComponent implements OnInit {
  nickname: string | null = null;
  fullName: string = '';
  role: string = 'Jugador';
  profilePhoto: string | null = null;

  activeTab: 'team' | 'events' | 'settings' = 'team';

  constructor(
    private authService: AuthService,
    private invitationService: TeamInvitationService
  ) {}


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

  openTab(tab: 'team' | 'events' | 'settings') {
    this.activeTab = tab;
  }



}
