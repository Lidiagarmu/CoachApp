import { Component, Input, Output, EventEmitter, OnChanges } from '@angular/core';
import { CommonModule, NgIf, NgFor } from '@angular/common';
import { Event as AppEvent } from '../../../../interfaces/event.model';
import { environment } from '../../../../../environments/enviroment';

@Component({
  selector: 'app-modal-event-details-player',
  standalone: true,
  imports: [CommonModule, NgIf, NgFor],
  templateUrl: './modal-event-details-player.component.html',
})
export class ModalEventDetailsPlayerComponent implements OnChanges {
  @Input() event?: AppEvent;
  @Output() close = new EventEmitter<void>();

  backendUrl = environment.apiUrl;
  selectedEvent?: AppEvent;

  ngOnChanges(): void {
    if (this.event) {
      this.openModal(this.event);
    }
  }

  openModal(event: AppEvent): void {
    // URLs completas para imágenes
    if (event.images) {
      event.images = event.images.map(img =>
        img.startsWith('http') ? img : `${this.backendUrl}${img}`
      );
    }

    // Escudo del equipo
    if (event.team?.shield && !event.team.shield.startsWith('http')) {
      event.team.shield = `${this.backendUrl}${event.team.shield}`;
    }

    this.selectedEvent = event;
  }

  getTeamShield(event: AppEvent): string | null {
    const shield = event.team?.shield ?? null;
    if (!shield) return null;
    return shield.startsWith('http') ? shield : `${this.backendUrl}${shield}`;
  }

  closeDetailsModal(): void {
    this.selectedEvent = undefined;
    this.close.emit();
  }
}
