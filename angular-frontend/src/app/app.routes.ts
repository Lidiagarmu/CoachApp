import { Routes } from '@angular/router';
import { HomeComponent } from './modules/home/home.component';
import { LoginComponent } from './modules/login/login.component';
import { RegisterComponent } from './modules/register/register.component';
import { CoachRegisterComponent } from './modules/register/coach-register/coach-register.component';
import { PlayerRegisterComponent } from './modules/register/player-register/player-register.component';
import { CoachDashboardComponent } from './modules/coach-dashboard/coach-dashboard.component';
import { PlayerDashboardComponent } from './modules/player-dashboard/player-dashboard.component';
import { AdminDashboardComponent } from './modules/admin-dashboard/admin-dashboard.component';
import { AuthGuard } from './guards/auth.guard';

export const routes: Routes = [
  { path: '', redirectTo: 'home', pathMatch: 'full' }, // raíz redirige a /home
  { path: 'home', component: HomeComponent },          // home público
  { path: 'login', component: LoginComponent },
  { path: 'register', component: RegisterComponent },
  { path: 'register/coach', component: CoachRegisterComponent },
  { path: 'register/player', component: PlayerRegisterComponent },
  { path: 'coach', component: CoachDashboardComponent, canActivate: [AuthGuard] },
  { path: 'player', component: PlayerDashboardComponent, canActivate: [AuthGuard] },
  { path: 'admin', component: AdminDashboardComponent, canActivate: [AuthGuard] },
  { path: '**', redirectTo: 'home' }                   // wildcard: rutas inválidas redirigen a home
];
