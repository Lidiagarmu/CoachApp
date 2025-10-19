import { Routes } from '@angular/router';
import { LoginComponent } from './modules/login/login.component';
import { RegisterComponent } from './modules/register/register.component';
import { CoachDashboardComponent } from './modules/coach-dashboard/coach-dashboard.component';
import { PlayerDashboardComponent } from './modules/player-dashboard/player-dashboard.component';
import { AdminDashboardComponent } from './modules/admin-dashboard/admin-dashboard.component';
import { AuthGuard } from './guards/auth.guard';

export const routes: Routes = [
  { path: 'login', component: LoginComponent },
  { path: 'register', component: RegisterComponent },
  { path: 'coach', component: CoachDashboardComponent, canActivate: [AuthGuard] },
  { path: 'player', component: PlayerDashboardComponent, canActivate: [AuthGuard] },
  { path: 'admin', component: AdminDashboardComponent, canActivate: [AuthGuard] },
  { path: '', redirectTo: 'login', pathMatch: 'full' },
  { path: '**', redirectTo: 'login' }
];
