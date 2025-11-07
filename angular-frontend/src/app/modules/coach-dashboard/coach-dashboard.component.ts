import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NavbarComponent } from '../../shared/components/navbar/navbar.component';
import { PlayerService, Player } from '../../services/player.service';
import { TeamInvitationService } from '../../services/team-invitation.service';
import { AuthService } from '../../services/auth.service';

import { CoachTeamComponent } from './coach-team/coach-team.component';


@Component({
  selector: 'app-coach-dashboard',
  standalone: true,
  imports: [
    CommonModule,
    NavbarComponent,
    CoachTeamComponent
  ],
  templateUrl: './coach-dashboard.component.html',
})
export class CoachDashboardComponent implements OnInit {
  players: Player[] = [];
  nickname: string | null = null;
  fullName: string = '';
  role: string = 'Entrenador';

  // 🔹 Ahora incluimos 'team' aunque la sección esté comentada, no da error
  activeTab: 'team' | 'players' | 'events' | 'settings' = 'players';

  constructor(
    private playerService: PlayerService,
    private invitationService: TeamInvitationService,
    private authService: AuthService
  ) {}

  ngOnInit(): void {
    this.loadPlayers();

    const user = this.authService.getUser();
    if (user) {
      this.nickname = user.nickname || '';
      this.fullName = user.fullName || '';
      this.role = user.roles?.includes('ROLE_COACH') ? 'Entrenador' : 'Jugador';
    }
  }

  loadPlayers(): void {
    this.playerService.getAvailablePlayers().subscribe({
      next: (res) => (this.players = res),
      error: (err) => console.error('Error al cargar jugadores', err),
    });
  }

  invitePlayer(playerId: number): void {
    this.invitationService.createInvitation(playerId).subscribe({
      next: () => {
        alert('✅ Invitación enviada correctamente');
        this.loadPlayers(); // refresca lista
      },
      error: (err) =>
        alert(err.error?.error || '❌ Error al enviar invitación'),
    });
  }

  // 🔹 Métodos de placeholder (ya listos para futuro uso)
  openTeamView(): void {
    this.activeTab = 'team';
  }

  openEventsView(): void {
    this.activeTab = 'events';
  }

  openSettingsView(): void {
    this.activeTab = 'settings';
  }
}
