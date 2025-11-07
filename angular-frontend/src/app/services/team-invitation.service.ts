import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface TeamInvitation {
  id: number;
  team: string;
  status: string; // pending | accepted | rejected
  createdAt: string;
}

@Injectable({
  providedIn: 'root'
})
export class TeamInvitationService {
  private apiUrl = 'http://localhost:8000/api/invitations';

  constructor(private http: HttpClient) {}

  // Enviar invitación a un jugador
  createInvitation(playerId: number): Observable<any> {
    return this.http.post(this.apiUrl, { playerId });
  }

  // Obtener invitaciones del jugador
  getPlayerInvitations(): Observable<TeamInvitation[]> {
    return this.http.get<TeamInvitation[]>(`${this.apiUrl}/player`);
  }

  // Responder invitación
  respondInvitation(id: number, action: 'accept' | 'reject'): Observable<any> {
    return this.http.patch(`${this.apiUrl}/${id}/respond`, { action });
  }
}
