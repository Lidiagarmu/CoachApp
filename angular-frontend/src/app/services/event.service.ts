import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Event as AppEvent } from '../interfaces/event.model';


export interface Event {
  id: string;
  title: string;
  description: string;
  date: string;
  time: string;
  duration: number;
  location_name: string;
  location_url: string;
  type: 'training' | 'match';
  team: string;
  images: string[];
  // Campos específicos de training o match opcionales
  training_type?: string;
  focus_area?: string;
  opponent?: string;
  match_type?: string;
}

@Injectable({
  providedIn: 'root'
})
export class EventService {
  private apiUrl = 'http://localhost:8000/api/events';

  constructor(private http: HttpClient) {}

  // 👉 Crear un evento
  createEvent(event: Partial<AppEvent>): Observable<any> {
    return this.http.post(this.apiUrl, event);
  }

  // 👉 Obtener eventos de un equipo
  getEventsByTeam(teamId: number | string): Observable<AppEvent[]> {
    return this.http.get<AppEvent[]>(`${this.apiUrl}/team/${teamId}`);
  }

  // 👉 Actualizar evento
  updateEvent(id: string, event: Partial<AppEvent>): Observable<any> {
    return this.http.put(`${this.apiUrl}/${id}`, event);
  }

  // 👉 Eliminar evento
  deleteEvent(id: string): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }

  // 👉 Subir imágenes
  uploadImages(eventId: string, files: File[]): Observable<any> {
    const formData = new FormData();
    files.forEach(file => formData.append('images[]', file));
    return this.http.post(`${this.apiUrl}/${eventId}/images`, formData);
  }
}
