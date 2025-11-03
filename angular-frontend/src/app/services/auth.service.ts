import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import { CookieService } from 'ngx-cookie-service';
import { environment } from '../../environments/enviroment';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private user: any = null;

  constructor(private http: HttpClient, private cookies: CookieService) {}

  // Función helper para decodificar JWT sin librerías
  private decodeToken(token: string): any {
    try {
      const payload = token.split('.')[1];        // Parte del payload
      const decodedPayload = atob(payload);      // Decodifica base64
      return JSON.parse(decodedPayload);         // Devuelve objeto JS
    } catch (e) {
      console.error('Error decoding token', e);
      return null;
    }
  }

  login(email: string, password: string): Observable<any> {
    return this.http.post<any>(`${environment.apiUrl}/login_check`, { email, password }).pipe(
      tap(res => {
        this.cookies.set('jwt_token', res.token, 1, '/', undefined, false, 'Lax');
        const decoded: any = this.decodeToken(res.token); // ✅ Decodificación manual
        this.user = decoded;
      })
    );
  }

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


  getUser(): any {
    return this.user;
  }

  logout() {
    this.cookies.delete('jwt_token', '/');
    this.user = null;
  }

  getToken(): string | null {
    return this.cookies.get('jwt_token') || null;
  }

  isLoggedIn(): boolean {
    return !!this.getToken();
  }
}
