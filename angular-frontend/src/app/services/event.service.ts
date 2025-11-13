import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Event as AppEvent } from '../interfaces/event.model';
import { map } from 'rxjs/operators';



@Injectable({
  providedIn: 'root'
})
export class EventService {
  private apiUrl = 'http://localhost:8000/apis/events';

  constructor(private http: HttpClient) {}

  // 👉 Crear un evento
  createEvent(event: Partial<AppEvent>): Observable<any> {
    return this.http.post(this.apiUrl, event, { withCredentials: true });
  }

  // 👉 Obtener eventos de un equipo

  getEventsByTeam(teamId: number | string): Observable<AppEvent[]> {
    return this.http
      .get<AppEvent[]>(`${this.apiUrl}/team/${teamId}`, { withCredentials: true })
      .pipe(
        map(events =>
          events.map(event => ({
            ...event,
            images: event.images?.map(img =>
              img.startsWith('http')
                ? img // si ya tiene dominio, la dejamos igual
                : `http://localhost:8000${img}` // si no, le agregamos el host sin /api
            ) || []
          }))
        )
      );
  }

  // 👉 Actualizar evento
  updateEvent(id: string, event: Partial<AppEvent>): Observable<any> {
    return this.http.put(`${this.apiUrl}/${id}`, event, { withCredentials: true });
  }

  // 👉 Eliminar evento
  deleteEvent(id: string): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`, { withCredentials: true });
  }

  // 👉 Subir imágenes
  uploadImages(eventId: string, files: File[]): Observable<any> {
    const formData = new FormData();
    files.forEach(file => formData.append('images[]', file));
    return this.http.post(`${this.apiUrl}/${eventId}/images`, formData, { withCredentials: true });
  }
}
