import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NavbarComponent } from '../../shared/components/navbar/navbar.component';
import { PlayerService, Player } from '../../services/player.service';
import { TeamInvitationService } from '../../services/team-invitation.service';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-coach-dashboard',
  standalone: true,
  imports: [CommonModule, NavbarComponent],
  templateUrl: './coach-dashboard.component.html',
})
export class CoachDashboardComponent implements OnInit {
  players: Player[] = [];
  nickname: string = '';
  fullName: string = '';
  role: string = 'Entrenador';

  activeTab: 'players' | 'events' | 'settings' = 'players';


  constructor(
    private playerService: PlayerService,
    private invitationService: TeamInvitationService,
    private authService: AuthService
  ) {}


  ngOnInit() {
    this.loadPlayers();

    const user = this.authService.getUser();
    if (user) {
      this.nickname = user.nickname || '';
      this.fullName = user.fullName || '';
      this.role = user.roles?.includes('ROLE_COACH') ? 'Entrenador' : 'Jugador';
    }
  }

  loadPlayers() {
    this.playerService.getAvailablePlayers().subscribe({
      next: (res) => (this.players = res),
      error: (err) => console.error(err),
    });
  }

  invitePlayer(playerId: number) {
    this.invitationService.createInvitation(playerId).subscribe({
      next: () => {
        alert('Invitación enviada correctamente');
        this.loadPlayers(); // refresca lista
      },
      error: (err) => alert(err.error?.error || 'Error al enviar invitación'),
    });
  }
}
