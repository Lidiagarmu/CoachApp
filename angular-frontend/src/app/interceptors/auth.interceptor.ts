import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { AuthService } from '../services/auth.service';

export const AuthInterceptor: HttpInterceptorFn = (req, next) => {
  const auth = inject(AuthService);
  const token = auth.getToken();

  // Excluir rutas públicas donde NO se debe enviar el JWT
  const isPublic =
    req.url.includes('/login_check') ||
    req.url.includes('/register');


  let cloned = req;

  // 👉 Siempre incluir cookies en TODAS las peticiones (importante para Symfony con sesión/JWT cookie)
  cloned = cloned.clone({
    withCredentials: true
  });


   // 👉 Si hay token (modo Bearer) y no es una ruta pública, añadimos header
  if (token && !isPublic) {
    cloned = cloned.clone({
      setHeaders: {
        Authorization: `Bearer ${token}`
      }
    });
  }

  return next(cloned);
  
};
