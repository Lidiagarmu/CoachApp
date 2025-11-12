import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormGroup, FormControl, Validators } from '@angular/forms';
import { EventService} from '../../../../services/event.service';
import { Event as AppEvent } from '../../../../interfaces/event.model';


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

  constructor(private eventService: EventService) {
    this.eventForm = new FormGroup({
      type: new FormControl('training', Validators.required),
      title: new FormControl('', Validators.required),
      description: new FormControl(''),
      date: new FormControl('', Validators.required),
      time: new FormControl('', Validators.required),
      duration: new FormControl(60, [Validators.required, Validators.min(1)]),
      location_name: new FormControl(''),
      location_url: new FormControl(''),
      training_type: new FormControl(''),
      focus_area: new FormControl(''),
      opponent: new FormControl(''),
      match_type: new FormControl(''),
    });
     // Suscribirse a cambios en tipo de evento
    this.eventForm.get('type')?.valueChanges.subscribe(value => {
      if (value === 'training') {
        this.eventForm.patchValue({ opponent: '', match_type: '' });
      } else if (value === 'match') {
        this.eventForm.patchValue({ training_type: '', focus_area: '' });
      }
    });
  }

  ngOnChanges(): void {
    if (this.eventToEdit) {
      this.eventForm.patchValue({
        ...this.eventToEdit
      });
    }
  }

  submit(): void {
    if (this.eventForm.invalid) return;

    const formValue = this.eventForm.value;
    const eventData: Partial<AppEvent> = {
      ...formValue,
      team: this.teamId.toString() // o solo team: this.teamId si tu backend espera number
    };

    const handleAfterSave = (eventId: string) => {
      if (this.selectedFiles && this.selectedFiles.length > 0) {
        // Subir imágenes
        this.eventService.uploadImages(eventId, this.selectedFiles).subscribe({
          next: () => {
            this.selectedFiles = [];       // limpiar selección
            this.formSaved.emit();         // emitir evento guardado
            this.closeForm();              // cerrar modal
          },
          error: err => console.error('Error subiendo imágenes', err)
        });
      } else {
        this.formSaved.emit();
        this.closeForm();
      }
    };

    if (this.eventToEdit) {
      // Actualizar evento existente
      this.eventService.updateEvent(this.eventToEdit.id!, eventData).subscribe({
        next: () => handleAfterSave(this.eventToEdit!.id!)
      });
    } else {
      // Crear nuevo evento
      this.eventService.createEvent(eventData).subscribe({
        next: (createdEvent: AppEvent) => handleAfterSave(createdEvent.id!)
      });
    }
  }





  closeForm(): void {
    this.formClosed.emit();
    this.eventForm.reset({ type: 'training', duration: 60 });
  }


  onFilesSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files) {
      this.selectedFiles = Array.from(input.files);
    }
  }

  // Eliminar archivo individual
  removeFile(file: File) {
    this.selectedFiles = this.selectedFiles.filter(f => f !== file);
  }

  filePreview(file: File): string {
  return URL.createObjectURL(file);
}



}
