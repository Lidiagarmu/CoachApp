import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule, NgIf, NgFor } from '@angular/common';
import { Event as AppEvent } from '../../../../interfaces/event.model';
import { EventService } from '../../../../services/event.service';
import { environment } from '../../../../../environments/enviroment';

@Component({
  selector: 'app-modal-event-details',
  standalone: true,
  imports: [CommonModule, NgIf, NgFor],
  templateUrl: './modal-event-details.component.html',
})
export class ModalEventDetailsComponent {
  @Input() event?: AppEvent; // Evento seleccionado
  @Input() trainings: AppEvent[] = [];
  @Input() games: AppEvent[] = [];

  @Output() close = new EventEmitter<void>();

  backendUrl = environment.apiUrl;

  // Modal interno para manejo del evento seleccionado
  selectedEvent?: AppEvent;

  imageIndex = 0;
  isImageViewerOpen = false;

  ngOnChanges(): void {
    if (this.event) {
      this.openModal(this.event);
    }
  }

  openModal(event: AppEvent): void {
      console.log('Modal abierto con event:', event);

    // Construir URLs completas de imágenes
    if (event.images) {
      event.images = event.images.map(img =>
        img.startsWith('http') ? img : `${this.backendUrl}${img}`
      );
    }

    // Construir URL del escudo del equipo
    if (event.team?.shield && !event.team.shield.startsWith('http')) {
      event.team.shield = `${this.backendUrl}${event.team.shield}`;
    }

    // Filtrar entrenamientos y juegos relacionados
    if (event.type === 'training') {
      this.trainings = [event];
      this.games = [];
    } else if (event.type === 'match') {
      this.games = [event];
      this.trainings = [];
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
    this.trainings = [];
    this.games = [];
    this.close.emit();
  }

  openImageViewer(i: number) {
    this.imageIndex = i;
    this.isImageViewerOpen = true;
  }

  closeImageViewer() {
    this.isImageViewerOpen = false;
  }

  nextImage() {
  if (!this.selectedEvent?.images) return;
  this.imageIndex = (this.imageIndex + 1) % this.selectedEvent.images.length;
  }

  prevImage() {
    if (!this.selectedEvent?.images) return;
    this.imageIndex =
      (this.imageIndex - 1 + this.selectedEvent.images.length) %
      this.selectedEvent.images.length;
  }

}
