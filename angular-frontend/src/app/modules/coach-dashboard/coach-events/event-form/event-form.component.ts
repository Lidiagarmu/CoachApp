import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormGroup, FormControl, Validators } from '@angular/forms';
import { EventService } from '../../../../services/event.service';
import { Event as AppEvent } from '../../../../interfaces/event.model';
import { ChangeDetectorRef } from '@angular/core';



@Component({
  selector: 'app-event-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './event-form.component.html'
})
export class EventFormComponent {
  @Input() teamId!: number;
  @Input() eventToEdit?: AppEvent;
  @Output() formClosed = new EventEmitter<void>();
  @Output() formSaved = new EventEmitter<void>();

  eventForm: FormGroup;
  selectedFiles: File[] = [];
  filePreviews: string[] = [];


  constructor(private eventService: EventService, private cdr: ChangeDetectorRef ) {
    this.eventForm = new FormGroup({
      type: new FormControl('', Validators.required),
      title: new FormControl('', Validators.required),
      description: new FormControl(''),
      date: new FormControl('', Validators.required),
      time: new FormControl('', Validators.required),
      duration: new FormControl(60, [Validators.required, Validators.min(1)]),
      location_name: new FormControl(''),
      location_url: new FormControl(''),
      training_type: new FormControl(''), // campo o gimnasio
      gym_focus: new FormControl(''), // fuerza, resistencia, preventivo, velocidad, pliometría
      focus_area: new FormControl(''), // tecnico, tactico, fisico o mixto
      opponent: new FormControl(''),
      match_type: new FormControl(''),
    });

    // Suscribirse a cambios en tipo de evento
    this.eventForm.get('type')?.valueChanges.subscribe(value => {
      if (value === 'training') {
        this.eventForm.patchValue({ opponent: '', match_type: '' });
      } else if (value === 'match') {
        this.eventForm.patchValue({ training_type: 'campo', gym_focus: '', focus_area: '' });
      }
    });

    // Suscribirse a cambios en tipo de entrenamiento (campo/gimnasio)
    this.eventForm.get('training_type')?.valueChanges.subscribe(value => {
      if (value === 'campo') {
        this.eventForm.patchValue({ gym_focus: '' });
      } else if (value === 'gimnasio') {
        this.eventForm.patchValue({ focus_area: '' });
      }
    });
  }

  ngOnChanges(): void {
    if (this.eventToEdit) {
      this.eventForm.patchValue({ ...this.eventToEdit });
    }
  }

  submit(): void {
    if (this.eventForm.invalid) return;

    const formValue = this.eventForm.value;
    const eventData: Partial<AppEvent> = {
      ...formValue,
        // Backend expects `team_id` (snake_case). Send both to be safe.
        team_id: this.teamId,
        teamId: this.teamId,
        description: formValue.description || '',
        location_name: formValue.location_name || '',
        location_url: formValue.location_url || ''
    };

    console.log('🟢 Enviando eventData:', eventData);

    const handleAfterSave = (eventId: number) => {
      if (this.selectedFiles.length > 0) {
        this.eventService.uploadImages(eventId, this.selectedFiles).subscribe({
          next: () => {
            this.selectedFiles = [];
            this.formSaved.emit();
            this.closeForm();
          },
          error: err => console.error('Error subiendo imágenes', err)
        });
      } else {
        this.formSaved.emit();
        this.closeForm();
      }
    };

    if (this.eventToEdit) {
      this.eventService.updateEvent(this.eventToEdit.id!, eventData).subscribe({
        next: () => handleAfterSave(this.eventToEdit!.id!),
        error: err => {
          console.error('Error actualizando evento', err);
          alert('Error actualizando evento: ' + (err?.message || err));
        }
      });
    } else {
      this.eventService.createEvent(eventData).subscribe({
        next: (createdEvent: any) => {
          // Backend may return different shapes: { id }, { event: { id } }, or the full event
          const id = createdEvent?.id ?? createdEvent?.event?.id ?? createdEvent;
          if (!id) {
            console.warn('Respuesta de creación inesperada:', createdEvent);
          }
          handleAfterSave(Number(id));
        },
        error: err => {
          console.error('Error creando evento', err);
          alert('Error creando evento: ' + (err?.message || err));
        }
      });
    }
  }

  closeForm(): void {
    this.formClosed.emit();
    this.eventForm.reset({ type: 'training', training_type: 'campo', duration: 60 });
  }

 onFilesSelected(event: Event): void {
  const input = event.target as HTMLInputElement;
  if (input.files) {
    this.selectedFiles = Array.from(input.files);
    this.filePreviews = this.selectedFiles.map(file => URL.createObjectURL(file));
    this.cdr.detectChanges();
  }
}

removeFile(file: File) {
  const index = this.selectedFiles.indexOf(file);
  if (index > -1) {
    this.selectedFiles.splice(index, 1);
    this.filePreviews.splice(index, 1);
  }
}

  filePreview(file: File): string {
    return URL.createObjectURL(file);
  }
}
