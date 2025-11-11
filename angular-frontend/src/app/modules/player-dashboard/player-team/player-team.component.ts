import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TeamInvitationService } from '../../../services/team-invitation.service';

@Component({
  selector: 'app-player-team',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './player-team.component.html',
})
export class PlayerTeamComponent implements OnInit {
  invitations: any[] = [];
  loading = false;
  error: string | null = null;
  selectedInvitation: any = null; // Para el modal

  constructor(private invitationService: TeamInvitationService) {}

  ngOnInit(): void {
    this.loadInvitations();
  }

  loadInvitations(): void {
    this.loading = true;
    this.error = null;

    this.invitationService.getPlayerInvitations().subscribe({
      next: (res) => {
        this.invitations = res.filter(inv => inv.status === 'pending');
        this.loading = false;
      },
      error: (err) => {
        this.loading = false;
        this.error = 'Error al cargar invitaciones.';
        console.error(err);
      },
    });
  }

  openInvitation(inv: any): void {
    this.selectedInvitation = inv;
  }

  closeModal(): void {
    this.selectedInvitation = null;
  }

  respond(action: 'accept' | 'reject'): void {
    if (!this.selectedInvitation) return;

    this.invitationService.respondInvitation(this.selectedInvitation.id, action).subscribe({
      next: (res) => {
        alert(res.message);
        this.selectedInvitation = null;
        this.loadInvitations();
      },
      error: (err) => {
        console.error(err);
        alert(err.error?.error || 'Error al responder invitación.');
      },
    });
  }
}
