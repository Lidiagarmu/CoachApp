import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { PlayerService, Player } from '../../../services/player.service';
import { TeamInvitationService } from '../../../services/team-invitation.service';

@Component({
  selector: 'app-coach-players',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './coach-players.component.html',
})
export class CoachPlayersComponent implements OnInit {
  players: Player[] = [];
  loading = false;
  error: string | null = null;
  success: string | null = null;

  constructor(
    private playerService: PlayerService,
    private invitationService: TeamInvitationService
  ) {}

  ngOnInit(): void {
    this.loadPlayers();
  }

  loadPlayers(): void {
    this.loading = true;
    this.error = null;

    this.playerService.getAvailablePlayers().subscribe({
      next: (res) => {
        this.players = res;
        this.loading = false;
      },
      error: (err) => {
        this.loading = false;
        this.error = 'Error al cargar jugadores disponibles.';
        console.error(err);
      },
    });
  }

  invitePlayer(playerId: number): void {
    if (!confirm('¿Enviar invitación a este jugador?')) return;

    this.invitationService.createInvitation(playerId).subscribe({
      next: () => {
        alert('✅ Invitación enviada correctamente');
        this.loadPlayers();
      },
      error: (err) => {
        console.error(err);
        alert(err.error?.error || ' Error al enviar invitación');
      },
    });
  }
}
