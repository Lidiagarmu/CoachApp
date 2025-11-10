import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NavbarComponent } from '../../shared/components/navbar/navbar.component';
import { AuthService } from '../../services/auth.service';
import { TeamInvitationService, TeamInvitation } from '../../services/team-invitation.service';

@Component({
  selector: 'app-player-dashboard',
  standalone: true,
  imports: [CommonModule, NavbarComponent],
  templateUrl: './player-dashboard.component.html',
})
export class PlayerDashboardComponent implements OnInit {
  nickname: string | null = null;
  fullName: string = '';
  role: string = 'Jugador';
  profilePhoto: string | null = null;

  activeTab: 'team' | 'events' | 'settings' = 'team';
  invitations: TeamInvitation[] = [];

  constructor(
    private authService: AuthService,
    private invitationService: TeamInvitationService
  ) {}

  ngOnInit(): void {
    const user = this.authService.getUser();
    if (user) {
      this.nickname = user.nickname || '';
      this.fullName = user.fullName || '';
      this.role = 'Jugador';
      this.profilePhoto = user.profilePhoto || null;
    }

    this.loadInvitations();
  }

  openTab(tab: 'team' | 'events' | 'settings') {
    this.activeTab = tab;
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
      error: (err) =>
        alert(err.error?.error || 'Error al responder invitación'),
    });
  }
}
