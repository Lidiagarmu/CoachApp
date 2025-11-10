import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NavbarComponent } from '../../shared/components/navbar/navbar.component';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-admin-dashboard',
  standalone: true,
  imports: [CommonModule, NavbarComponent],
  templateUrl: './admin-dashboard.component.html',
})
export class AdminDashboardComponent implements OnInit {
  nickname: string | null = null;
  fullName: string = '';
  role: string = 'Administrador';
  profilePhoto: string | null = null;

  activeTab: 'players' | 'coaches' | 'teams' | 'settings' = 'players';

  constructor(private authService: AuthService) {}

  ngOnInit(): void {
    const user = this.authService.getUser();
    if (user) {
      this.nickname = user.nickname || '';
      this.fullName = user.fullName || '';
      this.role = 'Administrador';
    }
  }

  openTab(tab: 'players' | 'coaches' | 'teams' | 'settings'): void {
    this.activeTab = tab;
  }
}
