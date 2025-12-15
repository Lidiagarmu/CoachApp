import { Component, Input, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule, NgIf } from '@angular/common';

import { AuthService } from '../../../services/auth.service';

import { HomeButtonComponent } from '../home-button/home-button.component';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [CommonModule, NgIf, HomeButtonComponent],
  templateUrl: './navbar.component.html',
})
export class NavbarComponent implements OnInit {

  @Input() showButton: boolean = false; //  input para mostrar el botón  atrás
  @Input() showHome: boolean = false;       //  input para mostrar el botón home


  constructor(
    private router: Router,
    private authService: AuthService
  ) {}

  ngOnInit(): void {
  }


}
