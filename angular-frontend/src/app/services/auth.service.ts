import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import { CookieService } from 'ngx-cookie-service'; // Requiere instalar ngx-cookie-service

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private user: any = null;

  constructor(private http: HttpClient, private cookies: CookieService) {}

  login(email: string, password: string): Observable<any> {
    return this.http.post<any>('/api/login_check', { email, password }).pipe(
      tap(res => {
        // Guardar token en cookie segura
        this.cookies.set('jwt_token', res.token, 1, '/', undefined, false, 'Lax');
        // Guardar info del usuario en memoria (opcional)
        this.user = res.user || null;
      })
    );
  }

 register(data: {
  email: string;
  password: string;
  type: string;
  fullName?: string;
  nickname?: string;
  age?: number;
  teamId?: number;
}): Observable<any> {
  const payload: any = { ...data };
  if (data.type === 'player') payload.teamId = data.teamId;
  return this.http.post<any>('/api/register', payload);
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
