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
  selectedInvitation: any = null;
  showAutoModal = false;

  constructor(private invitationService: TeamInvitationService) {}

  ngOnInit(): void {
    this.loadInvitations();
  }

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

    showInvitation(inv: any): void {
    this.selectedInvitation = inv;
    this.showAutoModal = true;
  }

  

  closeModal(): void {
    this.selectedInvitation = null;
    this.showAutoModal = false;
  }

  respond(action: 'accept' | 'reject'): void {
    if (!this.selectedInvitation) return;

    this.invitationService.respondInvitation(this.selectedInvitation.id, action).subscribe({
      next: (res) => {
        this.selectedInvitation = null;
        this.showAutoModal = false;
        this.loadInvitations();
      },
      error: (err) => {
        console.error(err);
        alert(err.error?.error || 'Error al responder invitación.');
      },
    });
  }
}
