import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { NavbarComponent } from '../../shared/components/navbar/navbar.component';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, NavbarComponent],
  templateUrl: './register.component.html'
})
export class RegisterComponent {
  hovered: 'coach' | 'player' | null = null;

  constructor(private router: Router) {}

  goTo(type: 'coach' | 'player') {
    this.router.navigate([`/register/${type}`]);
  }
}
