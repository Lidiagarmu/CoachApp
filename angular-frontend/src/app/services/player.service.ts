import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Player {
  id: number;
  fullName: string;
  nickname: string;
  position: string;
  number: number;
}

@Injectable({
  providedIn: 'root'
})
export class PlayerService {
  private apiUrl = '/api/player';

  constructor(private http: HttpClient) {}

  // Listar jugadores disponibles para el coach
  getAvailablePlayers(): Observable<Player[]> {
    return this.http.get<Player[]>(`${this.apiUrl}/available`);
  }
}
