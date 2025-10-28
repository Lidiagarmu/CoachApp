import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NavbarComponent } from '../../shared/components/navbar/navbar.component';

@Component({
  selector: 'app-admin-dashboard',
  standalone: true,
  imports: [CommonModule, NavbarComponent],
  templateUrl: './admin-dashboard.component.html',
})
export class AdminDashboardComponent {}
