import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TeamInvitationService } from '../../../services/team-invitation.service';
import { LoadingSpinnerComponent } from '../../../shared/components/loading-spinner/loading-spinner.component';

@Component({
  selector: 'app-player-team',
  standalone: true,
  imports: [CommonModule, LoadingSpinnerComponent],
  templateUrl: './player-team.component.html',
})
export class PlayerTeamComponent implements OnInit {
  invitations: any[] = [];
  playerTeam: any = null; // info del equipo si ya pertenece a uno
  loading = false;
  error: string | null = null;
 


  selectedInvitation: any = null;
  showAutoModal = false;

  constructor(private invitationService: TeamInvitationService) {}

  ngOnInit(): void {
    this.loadInvitations();
    this.loadPlayerTeam();
  }

  // Cargar invitaciones pendientes
  loadInvitations(): void {
    this.loading = true;
    this.error = null;

    this.invitationService.getPlayerInvitations().subscribe({
      next: (res) => {
        this.invitations = res.filter((inv) => inv.status === 'pending');
        this.loading = false;
      },
      error: (err) => {
        this.loading = false;
        this.error = 'Error al cargar invitaciones.';
        console.error(err);
      },
    });
  }

  // Cargar info del equipo del jugador
  loadPlayerTeam(): void {
    this.invitationService.getPlayerTeam().subscribe({
      next: (team) => {
        this.playerTeam = team;
      },
      error: (err) => {
        console.error('Error al cargar el equipo:', err);
        this.playerTeam = null;
      }
    });
  }


  // Mostrar modal al pulsar "Mostrar"
  showInvitation(inv: any): void {
    this.selectedInvitation = inv;
    this.showAutoModal = true;
  }

  // Cerrar modal
  closeModal(): void {
    this.selectedInvitation = null;
    this.showAutoModal = false;
  }

  // Responder invitación
  respond(action: 'accept' | 'reject'): void {
    if (!this.selectedInvitation) return;

    this.invitationService.respondInvitation(this.selectedInvitation.id, action).subscribe({
      next: () => {
        this.selectedInvitation = null;
        this.showAutoModal = false;

        if (action === 'accept') {
          // Si acepta, eliminamos las invitaciones y cargamos info del equipo
          this.invitations = [];
          this.loadPlayerTeam();
        } else {
          this.loadInvitations();
        }
      },
      error: (err) => {
        console.error(err);
        alert(err.error?.error || 'Error al responder invitación.');
      },
    });
  }
}
