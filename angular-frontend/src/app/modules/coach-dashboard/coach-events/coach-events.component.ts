import { Component, OnInit } from '@angular/core';
import { EventService } from '../../../services/event.service';
import { EventFormComponent } from './event-form/event-form.component';
import { CommonModule, NgIf, NgFor } from '@angular/common';
import { ReactiveFormsModule } from '@angular/forms';
import { Event as AppEvent } from '../../../interfaces/event.model';

@Component({
  selector: 'app-coach-events',
  standalone: true,
  imports: [EventFormComponent, CommonModule, ReactiveFormsModule, NgIf, NgFor],
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

  constructor(private eventService: EventService) {}

  ngOnInit(): void {
    this.loadEvents();
  }

  loadEvents(): void {
    this.eventService.getEventsByTeam(this.teamId).subscribe(events => {
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

  deleteEvent(id: string): void {
    this.eventService.deleteEvent(id).subscribe(() => this.loadEvents());
  }

  onFilesSelected(event: any): void {
    this.selectedFiles = Array.from(event.target.files);
  }

  uploadSelectedImages(eventId: string): void {
    if (!this.selectedFiles || this.selectedFiles.length === 0) return;

    this.eventService.uploadImages(eventId, this.selectedFiles).subscribe(() => {
      this.selectedFiles = [];
      this.loadEvents();
    });
  }
}
