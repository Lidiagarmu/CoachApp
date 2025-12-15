import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { PlayerService, Player } from '../../../services/player.service';
import { TeamInvitationService } from '../../../services/team-invitation.service';

import { LoadingSpinnerComponent } from '../../../shared/components/loading-spinner/loading-spinner.component';


//Esto extiende el modelo de Player solo dentro de este componente
interface PlayerWithInvitationState extends Player {
  inviting?: boolean;
  invited?: boolean;
}


@Component({
  selector: 'app-coach-players',
  standalone: true,
  imports: [CommonModule, LoadingSpinnerComponent],
  templateUrl: './coach-players.component.html',
})
export class CoachPlayersComponent implements OnInit {
  players: PlayerWithInvitationState[] = [];
  loading = false;
  error: string | null = null;

  // modales
  showConfirmModal = false;
  showSuccessModal = false;
  showPendingModal = false;
  selectedPlayer: PlayerWithInvitationState | null = null;


  constructor(
    private playerService: PlayerService,
    private invitationService: TeamInvitationService
  ) {}

  ngOnInit(): void {
    this.checkViewport();
    window.addEventListener('resize', () => this.checkViewport());
    this.loadPlayers();
  }

  loadPlayers(): void {
    this.loading = true;
    this.error = null;

    this.playerService.getAvailablePlayers().subscribe({
      next: (res) => {
        // añadimos campos extra para manejar el estado local
        this.players = res.map((p) => ({ ...p, inviting: false }));
        this.loading = false;
      },
      error: (err) => {
        this.loading = false;
        this.error = 'Error al cargar jugadores disponibles.';
        console.error(err);
      },
    });
  }

  openConfirmModal(player: PlayerWithInvitationState): void {
    this.selectedPlayer = player;
    this.showConfirmModal = true;
  }

  sendInvitation(): void {
    if (!this.selectedPlayer) return;

    const player = this.selectedPlayer;
    player.inviting = true;
    this.showConfirmModal = false;

    this.invitationService.createInvitation(player.id).subscribe({
      next: () => {
        player.inviting = false;
        player.invited = true;
        this.showSuccessModal = true;
      },
      error: (err) => {
        player.inviting = false;
        console.error(err);

        if (err.status === 409) {
          // ya está pendiente
          player.invited = true;
          this.showPendingModal = true;
        } else {
          this.error = err.error?.error || 'Error al enviar invitación.';
        }
      },
    });
  }

  closeModals(): void {
    this.showConfirmModal = false;
    this.showSuccessModal = false;
    this.showPendingModal = false;
  }



  currentPage = 1;

  isMobile = false;

  pageSizeMobile = 4;
  pageSizeDesktop = 9;


  get pageSize() {
    return window.innerWidth < 640
      ? this.pageSizeMobile
      : this.pageSizeDesktop;
  }

  get totalPages(): number {
    return Math.ceil(this.players.length / this.pageSize);
  }

  get paginatedPlayers() {
    const start = (this.currentPage - 1) * this.pageSize;
    return this.players.slice(start, start + this.pageSize);
  }

  changePage(page: number) {
    this.currentPage = page;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  checkViewport(): void {
  const wasMobile = this.isMobile;
  this.isMobile = window.matchMedia('(max-width: 639px)').matches;

  // Si cambia de desktop a móvil, reseteamos página
  if (wasMobile !== this.isMobile) {
    this.currentPage = 1;
  }
}

}
