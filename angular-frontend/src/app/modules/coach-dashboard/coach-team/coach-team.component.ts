import { Component, OnInit, HostListener } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { TeamService, Team, Player } from '../../../services/team.service';
import { LoadingSpinnerComponent } from '../../../shared/components/loading-spinner/loading-spinner.component';

@Component({
  selector: 'app-coach-team',
  standalone: true,
  imports: [CommonModule, FormsModule, LoadingSpinnerComponent],
  templateUrl: './coach-team.component.html'
})
export class CoachTeamComponent implements OnInit {
  team: Team | null = null;
  loading = false;
  error = '';
  success = '';
  editMode = false;

  createForm = { name: '' };
  selectedFile: File | null = null; // Para subida de escudo
  previewUrl: string | null = null; // ← URL temporal para mostrar el preview


  constructor(private teamService: TeamService) {}

  ngOnInit(): void {
    this.loadTeam();
  }

  // Cargar equipo existente
  loadTeam(): void {
    this.loading = true;
    this.teamService.getTeam().subscribe({
      next: (team) => {
        this.team = team;
        this.loading = false;
      },
      error: () => {
        this.team = null;
        this.loading = false;
      }
    });
  }

  onFileSelected(event: any): void {
    const file = event.target.files?.[0];
       if (file) {
    this.selectedFile = file;

    // Generar URL temporal para preview
    const reader = new FileReader();
    reader.onload = (e: any) => {
      this.previewUrl = e.target.result;
    };
    reader.readAsDataURL(file);
  } else {
    this.selectedFile = null;
    this.previewUrl = null;
  }
  }

  // Función para eliminar la imagen seleccionada antes de enviar
  removeSelectedImage(): void {
    this.selectedFile = null;
    this.previewUrl = null;
  }


  // Crear nuevo equipo
  createTeam(): void {
    this.error = '';
    this.success = '';
    this.loading = true;

    const formData = new FormData();
    formData.append('name', this.createForm.name);
    if (this.selectedFile) {
      formData.append('shield', this.selectedFile);
    }
    console.log('🟢 Archivo seleccionado antes de enviar:', this.selectedFile);


    this.teamService.createTeam(formData).subscribe({
      next: (res) => {
        this.team = res;
        this.loading = false;
        this.success = '✅ Equipo creado correctamente';
        this.createForm.name = '';
        this.selectedFile = null;
      },
      error: (err) => {
        this.error = err.error?.error || 'Error al crear el equipo';
        this.loading = false;
      }
    });
  }

  // Actualizar equipo existente
  updateTeam(): void {
    if (!this.team) return;

    const formData = new FormData();
    formData.append('name', this.team.name || '');
    if (this.selectedFile) {
      formData.append('shield', this.selectedFile);
    }

    this.teamService.updateTeam(this.team.id, formData).subscribe({
      next: () => {
        this.success = '✅ Cambios guardados';
        this.editMode = false;
        this.selectedFile = null;
        this.loadTeam(); // recargar equipo actualizado
      },
      error: () => {
        this.error = 'Error al actualizar equipo';
      }
    });
  }

  // Eliminar equipo
  deleteTeam(): void {
    if (!this.team) return;
    if (!confirm('¿Seguro que quieres eliminar este equipo?')) return;

    this.teamService.deleteTeam(this.team.id).subscribe({
      next: () => {
        this.team = null;
        this.success = '🗑️ Equipo eliminado';
      },
      error: () => {
        this.error = 'Error al eliminar el equipo';
      }
    });
  }

  currentPage = 1;
pageSizeMobile = 5;
pageSizeDesktop = 9;
isMobile = window.innerWidth < 640;

@HostListener('window:resize', [])
onResize() {
  this.isMobile = window.innerWidth < 640;
  this.currentPage = 1;
}

get pageSize(): number {
  return this.isMobile ? this.pageSizeMobile : this.pageSizeDesktop;
}

get totalPages(): number {
  if (!this.team?.players) return 0;
  return Math.ceil(this.team.players.length / this.pageSize);
}

get paginatedPlayers(): Player[] {
  if (!this.team?.players) return [];
  const start = (this.currentPage - 1) * this.pageSize;
  return this.team.players.slice(start, start + this.pageSize);
}

// Para mostrar máximo 5 páginas como Google
get visiblePages(): number[] {
  const total = this.totalPages;
  if (total <= 5) return Array.from({ length: total }, (_, i) => i + 1);

  let start = Math.max(this.currentPage - 2, 1);
  let end = Math.min(start + 4, total);
  if (end - start < 4) start = end - 4;
  return Array.from({ length: end - start + 1 }, (_, i) => start + i);
}

changePage(page: number): void {
  if (page < 1 || page > this.totalPages) return;
  this.currentPage = page;
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

}
