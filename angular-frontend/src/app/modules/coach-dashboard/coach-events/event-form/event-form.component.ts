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
  @Output() imageUploadError = new EventEmitter<string>();


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

  onTypeChange(event: Event): void {
    const val = (event.target as HTMLSelectElement).value;
    this.eventForm.get('type')?.setValue(val);
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
    console.log('📸 Archivos seleccionados:', this.selectedFiles);
    console.log('📊 Total de archivos:', this.selectedFiles.length);

    const handleAfterSave = (eventId: number) => {
      if (this.selectedFiles.length > 0) {
        console.log('⬆️ Iniciando subida de imágenes para evento:', eventId);
        console.log('📁 Archivos a subir:', this.selectedFiles.map(f => ({ name: f.name, size: f.size, type: f.type })));
        
        this.eventService.uploadImages(eventId, this.selectedFiles).subscribe({
          next: (response) => {
            console.log('✅ Imágenes subidas exitosamente:', response);
            this.selectedFiles = [];
            this.formSaved.emit();
            this.closeForm();
          },
           
          error: err => {
              console.error('❌ Error subiendo imágenes', err);

              let rawMsg =
                err?.error?.error ||
                err?.error?.message ||
                err?.message ||
                'Error desconocido al subir las imágenes';

              let cleanMsg = '';

              if (rawMsg.includes('exceeds your upload_max_filesize')) {

                const fileMatch = rawMsg.match(/file\s+"([^"]+)"/i);
                const fileName = fileMatch ? fileMatch[1] : 'uno de los archivos';

                const limitMatch = rawMsg.match(/limit is ([^)]+)\)/i);
                let limit = limitMatch ? limitMatch[1] : null;

                if (limit && limit.toLowerCase().includes('kib')) {
                  const kb = parseInt(limit);
                  const mb = (kb / 1024).toFixed(1);
                  limit = `${mb} MB`;
                }

                cleanMsg = `El archivo "${fileName}" supera el tamaño máximo permitido (${limit}).`;
              } else {
                cleanMsg = rawMsg;
              }

              // ⛔ 1. borrar el evento recién creado
              this.eventService.deleteEvent(eventId).subscribe({
                next: () => console.log("🗑 Evento revertido por fallo en imágenes"),
                error: err2 => console.error("⚠ Error al borrar evento fallido:", err2)
              });

              // ⛔ 2. mostrar error al modal
              this.imageUploadError.emit(cleanMsg);

              // ⛔ 3. NO cerrar form
          }

        });
      } else {
        console.log('ℹ️ Sin imágenes para subir');
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
          console.log('🆕 Evento creado con ID:', id);
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
    console.log('🎯 onFilesSelected llamado');
    console.log('📥 input.files.length:', input.files.length);
    
    // Validate each file
    const validFiles: File[] = [];
    for (let i = 0; i < input.files.length; i++) {
      const file = input.files[i];
      
      console.log(`📎 Archivo ${i}:`, {
        name: file.name,
        size: file.size,
        type: file.type,
        lastModified: file.lastModified,
      });
      
      // Check if file has a valid name
      if (!file.name || file.name.trim() === '') {
        console.warn(`⚠️ Archivo ${i} sin nombre válido, ignorado`);
        continue;
      }
      
      // Check file size (max 5MB per image)
      if (file.size > 5 * 1024 * 1024) {
        console.warn(`⚠️ Archivo ${i} "${file.name}" es demasiado grande`);
        alert(`Archivo "${file.name}" es demasiado grande (máx 5MB)`);
        continue;
      }
      
      // Check file type
      const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
      if (!validTypes.includes(file.type)) {
        console.warn(`⚠️ Archivo ${i} "${file.name}" tipo inválido:`, file.type);
        alert(`Archivo "${file.name}" no es una imagen válida (JPG, PNG, WebP, GIF)`);
        continue;
      }
      
      validFiles.push(file);
      console.log(`✅ Archivo ${i} "${file.name}" es válido`);
    }
    
    console.log('📊 Resultado:', { totalSeleccionados: input.files.length, validos: validFiles.length });
    
    if (validFiles.length === 0) {
      alert('Ninguno de los archivos seleccionados es válido');
      return;
    }
    
    this.selectedFiles = validFiles;
    this.filePreviews = this.selectedFiles.map(file => URL.createObjectURL(file));
    this.cdr.detectChanges();
    
    console.log('✨ Archivos lista para enviar:', this.selectedFiles.map(f => f.name));
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
