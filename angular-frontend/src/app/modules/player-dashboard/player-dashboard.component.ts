import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NavbarComponent } from '../../shared/components/navbar/navbar.component';
import { TeamInvitationService, TeamInvitation } from '../../services/team-invitation.service';

@Component({
  selector: 'app-player-dashboard',
  standalone: true,
  imports: [CommonModule, NavbarComponent],
  templateUrl: './player-dashboard.component.html',
})
export class PlayerDashboardComponent implements OnInit {
  invitations: TeamInvitation[] = [];

  constructor(private invitationService: TeamInvitationService) {}

  ngOnInit() {
    this.loadInvitations();
  }

  loadInvitations() {
    this.invitationService.getPlayerInvitations().subscribe({
      next: (res) => (this.invitations = res),
      error: (err) => console.error(err),
    });
  }

  respond(id: number, action: 'accept' | 'reject') {
    this.invitationService.respondInvitation(id, action).subscribe({
      next: () => {
        alert(`Invitación ${action} correctamente`);
        this.loadInvitations();
      },
      error: (err) => alert(err.error?.error || 'Error al responder invitación'),
    });
  }
}
