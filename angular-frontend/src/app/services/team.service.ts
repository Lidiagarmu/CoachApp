import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Player {
  id: number;
  fullName: string;
  nickname?: string;
}

export interface Team {
  id: number;
  name: string;
  shield?: string;
  players?: Player[];
}

@Injectable({ providedIn: 'root' })
export class TeamService {
  private baseUrl = '/api/team';

  constructor(private http: HttpClient) {}

  createTeam(data: { name: string; shield?: string }): Observable<Team> {
    return this.http.post<Team>(this.baseUrl, data);
  }

  getTeam(): Observable<Team> {
    return this.http.get<Team>(this.baseUrl);
  }

  updateTeam(id: number, data: Partial<Team>): Observable<any> {
    return this.http.put(`${this.baseUrl}/${id}`, data);
  }

  deleteTeam(id: number): Observable<any> {
    return this.http.delete(`${this.baseUrl}/${id}`);
  }

  addPlayer(teamId: number, playerId: number): Observable<any> {
    return this.http.post(`${this.baseUrl}/${teamId}/add-player`, { playerId });
  }

  removePlayer(teamId: number, playerId: number): Observable<any> {
    return this.http.patch(`${this.baseUrl}/${teamId}/remove-player`, { playerId });
  }
}
