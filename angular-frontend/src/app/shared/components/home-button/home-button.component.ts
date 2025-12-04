import { Component } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-home-button',
  standalone: true,
  templateUrl: './home-button.component.html'
})
export class HomeButtonComponent {

  constructor(private router: Router) {}

  goHome() {
    this.router.navigate(['/home']);
  }
}
