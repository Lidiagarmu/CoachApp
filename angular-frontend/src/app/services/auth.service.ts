import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import { CookieService } from 'ngx-cookie-service'; // Requiere instalar ngx-cookie-service
import { environment } from '../../environments/enviroment';
import * as jwt_decode from 'jwt-decode';


@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private user: any = null;

  constructor(private http: HttpClient, private cookies: CookieService) {}



login(email: string, password: string): Observable<any> {
  return this.http.post<any>(`${environment.apiUrl}/login_check`, { email, password }).pipe(
    tap(res => {
      this.cookies.set('jwt_token', res.token, 1, '/', undefined, false, 'Lax');
      // Decodificar JWT para obtener roles
      const decoded: any = (jwt_decode as any)(res.token);
      this.user = decoded; // ahora tiene roles y cualquier claim que hayas puesto en el token
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
  return this.http.post<any>(`${environment.apiUrl}/register`, data);
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
