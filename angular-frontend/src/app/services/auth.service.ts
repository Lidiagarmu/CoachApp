import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap, switchMap, of } from 'rxjs';
import { CookieService } from 'ngx-cookie-service';
import { environment } from '../../environments/enviroment';
import { User } from '../interfaces/user.model';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private user: User | null = null;

  constructor(private http: HttpClient, private cookies: CookieService) {}

  // Función helper para decodificar JWT sin librerías
  private decodeToken(token: string): any {
    try {
      const payload = token.split('.')[1];
      const decodedPayload = atob(payload);
      return JSON.parse(decodedPayload);
    } catch (e) {
      console.error('Error decoding token', e);
      return null;
    }
  }

  // 🔹 Login y obtener usuario completo
  login(email: string, password: string): Observable<User> {
    return this.http.post<{ token: string }>(
      `${environment.apiUrl}/login_check`,
      { email, password },
      { withCredentials: true }
    ).pipe(
      tap(res => this.cookies.set('jwt_token', res.token, 1, '/', undefined, false, 'Lax')),
      switchMap(() => this.http.get<User>(`${environment.apiUrl}/me`, { withCredentials: true })),
      tap(user => this.user = user)
    );
  }

  // 🔹 Registrar usuario
  register(data: {
    email: string | null | undefined;
    password: string | null | undefined;
    type: string;
    fullName?: string | null | undefined;
    nickname?: string | null | undefined;
    age?: number | null | undefined;
    yearsExperience?: number | null | undefined;
    teamName?: string | null | undefined;
    teamId?: number | null | undefined;
  }): Observable<any> {
    const payload: any = { ...data };
    if (data.type === 'player') payload.teamId = data.teamId;
    return this.http.post<any>(`${environment.apiUrl}/register`, payload);
  }

  // 🔹 Obtener usuario en memoria o desde token
  getUser(): User | null {
    if (this.user) return this.user;

    const token = this.getToken();
    if (!token) return null;

    const decoded = this.decodeToken(token);
    if (!decoded) return null;

    // ⚡ Solo se puede obtener el nickname si lo incluimos en el JWT
    this.user = {
      id: decoded.id ?? 0,
      email: decoded.username ?? '',
      nickname: decoded.nickname ?? '',
      fullName: decoded.fullName ?? '',
      roles: decoded.roles ?? []
    };
    return this.user;
  }

  // AuthService
  fetchUserFromApi(): Observable<User> {
    return this.http.get<User>(`${environment.apiUrl}/me`, { withCredentials: true }).pipe(
      tap(user => this.user = user) // guarda el usuario en memoria
    );
  }

  // 🔹 Logout
  logout() {
    this.cookies.delete('jwt_token', '/');
    this.user = null;
  }

  // 🔹 Obtener token
  getToken(): string | null {
    return this.cookies.get('jwt_token') || null;
  }

  isLoggedIn(): boolean {
    return !!this.getToken();
  }
}
