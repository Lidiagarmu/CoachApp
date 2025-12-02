import { Component, OnInit } from '@angular/core';
import { EventService } from '../../../services/event.service';
import { EventFormComponent } from './event-form/event-form.component';
import { ModalEventDetailsComponent } from './modal-event-details/modal-event-details.component';
import { CommonModule, NgIf, NgFor } from '@angular/common';
import { ReactiveFormsModule } from '@angular/forms';
import { Event as AppEvent } from '../../../interfaces/event.model';
import { environment } from '../../../../environments/enviroment';

@Component({
  selector: 'app-coach-events',
  standalone: true,
  imports: [
    EventFormComponent,
    ModalEventDetailsComponent, 
    CommonModule, 
    ReactiveFormsModule, 
    NgIf, 
    NgFor
  ],
  templateUrl: './coach-events.component.html'
})
export class CoachEventsComponent implements OnInit {
  events: AppEvent[] = [];
  trainings: AppEvent[] = [];
  games: AppEvent[] = [];
  selectedFiles: File[] = [];
  teamId = 1;

  showFormModal = false;
  eventToEdit?: AppEvent;

  showDetailsModal = false;  // <-- modal de detalles
  selectedEvent?: AppEvent;  // <-- evento seleccionado para ver detalles

  backendUrl = environment.apiUrl; 

  constructor(private eventService: EventService) {}

  ngOnInit(): void {
    this.loadEvents();
  }

  loadEvents(): void {
    this.eventService.getEventsByTeam(this.teamId).subscribe(events => {
      // 🧠 construir URLs completas para imágenes
      events.forEach(event => {
        if (event.images) {
          event.images = event.images.map(img =>
            img.startsWith('http') ? img : `${this.backendUrl}${img}`
          );
        }
      });

      this.events = events;
      this.trainings = events.filter(e => e.type === 'training');
      this.games = events.filter(e => e.type === 'match');
    });
  }

  openCreateEventModal(): void {
    this.eventToEdit = undefined;
    this.showFormModal = true;
  }

  editEvent(event: AppEvent): void {
    this.eventToEdit = event;
    this.showFormModal = true;
  }

  closeForm(): void {
    this.showFormModal = false;
    this.eventToEdit = undefined;
    this.selectedFiles = [];
  }

  onFormSaved(): void {
    this.loadEvents();
    this.closeForm();
  }

  deleteEvent(id: number) {
    this.eventService.deleteEvent(id).subscribe({
      next: () => this.loadEvents(),
      error: (err) => console.error(err)
    });
  }

  onFilesSelected(event: any): void {
    this.selectedFiles = Array.from(event.target.files);
  }

  uploadSelectedImages(eventId: number): void {
    if (!this.selectedFiles || this.selectedFiles.length === 0) return;

    this.eventService.uploadImages(eventId, this.selectedFiles).subscribe(() => {
      this.selectedFiles = [];
      this.loadEvents();
    });
  }

  getTeamShield(event: AppEvent): string | null {
    const shield = event.team?.shield ?? null;
    if (!shield) return null;
    return shield.startsWith('http') ? shield : `${this.backendUrl}${shield}`;
  }

  // ===============================================
  // Nuevo: abrir modal de detalles
  // ===============================================
  openModal(event: AppEvent): void {
    this.selectedEvent = event;
    this.showDetailsModal = true;
  }

  closeDetailsModal(): void {
    this.selectedEvent = undefined;
    this.showDetailsModal = false;
  }
}
