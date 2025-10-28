import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NavbarComponent } from '../../shared/components/navbar/navbar.component';

@Component({
  selector: 'app-coach-dashboard',
  standalone: true,
  imports: [CommonModule, NavbarComponent],
  templateUrl: './coach-dashboard.component.html',
})
export class CoachDashboardComponent {}
