import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { TeamService, Team, Player } from '../../../services/team.service';

@Component({
  selector: 'app-coach-team',
  standalone: true,
  imports: [CommonModule, FormsModule],
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

  // Capturar archivo seleccionado
  onFileSelected(event: any): void {
    const file = event.target.files[0];
    if (file) this.selectedFile = file;
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
}
