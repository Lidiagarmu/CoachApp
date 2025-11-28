import { Component, OnInit, Input, OnChanges, SimpleChanges } from '@angular/core';
import { EventService } from '../../../services/event.service';
import { Event as AppEvent} from '../../../interfaces/event.model'; 
import { CommonModule } from '@angular/common';


@Component({
  selector: 'app-player-events',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './player-events.component.html',
})
export class PlayerEventsComponent implements OnInit, OnChanges {
  @Input() teamId?: number;
  trainings: AppEvent[] = [];
  matches: AppEvent[] = [];
  loading = false;
  errorMessage = '';

  private _playerId?: number;

   @Input() 
  set playerId(value: number | undefined) {
    this._playerId = value;
    if (this._playerId) this.loadEvents();
  }
  get playerId(): number | undefined {
    return this._playerId;
  }


  constructor(private eventService: EventService) {}

  ngOnInit(): void {
    if (this.playerId) this.loadEvents();
  }


    ngOnChanges(changes: SimpleChanges) {
    if (changes['playerId'] && this.playerId) {
      this.loadEvents();
    }
  }

  
  loadEvents(): void {
    if (!this.playerId) return;

    this.loading = true;
    this.eventService.getEventsByPlayer(this.playerId).subscribe({
      next: events => {
        console.log('💡 Eventos crudos del API:', events);
        this.trainings = events.filter(e => e.type === 'training');
        this.matches = events.filter(e => e.type === 'match');
        console.log('💡 Entrenamientos filtrados:', this.trainings);
        console.log('💡 Partidos filtrados:', this.matches);
        this.loading = false;
      },
      error: err => {
        console.error('Error cargando eventos:', err);
        this.errorMessage = 'No se pudieron cargar los eventos.';
        this.loading = false;
      }
    });
  }

}