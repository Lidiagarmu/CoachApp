import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NavbarComponent } from '../../shared/components/navbar/navbar.component';

@Component({
  selector: 'app-player-dashboard',
  standalone: true,
  imports: [CommonModule, NavbarComponent],
  templateUrl: './player-dashboard.component.html',
})
export class PlayerDashboardComponent {}
