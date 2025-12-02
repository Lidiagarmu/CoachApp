import { Component, OnInit, Input, OnChanges, SimpleChanges } from '@angular/core';
import { EventService } from '../../../services/event.service';
import { Event as AppEvent } from '../../../interfaces/event.model';
import { CommonModule } from '@angular/common';
import { ModalEventDetailsPlayerComponent } from './modal-event-details-player/modal-event-details-player.component';
import { environment } from '../../../../environments/enviroment';

@Component({
  selector: 'app-player-events',
  standalone: true,
  imports: [CommonModule, ModalEventDetailsPlayerComponent],
  templateUrl: './player-events.component.html',
})
export class PlayerEventsComponent implements OnInit, OnChanges {
  @Input() teamId?: number;
  trainings: AppEvent[] = [];
  games: AppEvent[] = [];
  loading = false;
  errorMessage = '';

  showDetailsModal = false;
  selectedEvent?: AppEvent;

  private _playerId?: number;

  @Input()
  set playerId(value: number | undefined) {
    this._playerId = value;
    if (this._playerId) this.loadEvents();
  }
  get playerId(): number | undefined {
    return this._playerId;
  }

  backendUrl = environment.apiUrl;

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
        // Construir URLs completas para imágenes
        events.forEach(event => {
          if (event.images) {
            event.images = event.images.map(img =>
              img.startsWith('http') ? img : `${this.backendUrl}${img}`
            );
          }
        });

        this.trainings = events.filter(e => e.type === 'training');
        this.games = events.filter(e => e.type === 'match'); 
        this.loading = false;
      },
      error: err => {
        console.error('Error cargando eventos:', err);
        this.errorMessage = 'No se pudieron cargar los eventos.';
        this.loading = false;
      }
    });
  }

  openModal(event: AppEvent): void {
    this.selectedEvent = event;
    this.showDetailsModal = true;
  }

  closeDetailsModal(): void {
    this.selectedEvent = undefined;
    this.showDetailsModal = false;
  }

  getTeamShield(event: AppEvent): string | null {
    const shield = event.team?.shield ?? null;
    if (!shield) return null;
    return shield.startsWith('http') ? shield : `${this.backendUrl}${shield}`;
  }
}
