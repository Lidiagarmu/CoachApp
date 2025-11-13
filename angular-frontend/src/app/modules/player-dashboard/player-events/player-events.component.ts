import { Component, OnInit, Input } from '@angular/core';
import { EventService } from '../../../services/event.service';
import { Event } from '../../../interfaces/event.model'; 
import { CommonModule } from '@angular/common';


@Component({
  selector: 'app-player-events',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './player-events.component.html',
})
export class PlayerEventsComponent implements OnInit {
  @Input() teamId?: number;
  trainings: Event[] = [];
  matches: Event[] = [];
  loading = false;
  errorMessage = '';

  constructor(private eventService: EventService) {}

  ngOnInit(): void {
    if (this.teamId) this.loadEvents();
  }

  loadEvents(): void {
    this.loading = true;
    this.eventService.getEventsByTeam(this.teamId!).subscribe({
      next: (events) => {
        this.trainings = events.filter(e => e.type === 'training');
        this.matches = events.filter(e => e.type === 'match');
        this.loading = false;
      },
      error: (err) => {
        console.error('Error cargando eventos:', err);
        this.errorMessage = 'No se pudieron cargar los eventos.';
        this.loading = false;
      },
    });
  }
}
