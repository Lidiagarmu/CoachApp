import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { TeamService, Team } from '../../../services/team.service';

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
  createForm = { name: '', shield: '' };
  editMode = false;

  constructor(private teamService: TeamService) {}

  ngOnInit(): void {
    this.loadTeam();
  }

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
      },
    });
  }

  createTeam(): void {
    this.error = '';
    this.success = '';
    this.loading = true;
    this.teamService.createTeam(this.createForm).subscribe({
      next: (res) => {
        this.team = res;
        this.loading = false;
        this.success = '✅ Equipo creado correctamente';
      },
      error: (err) => {
        this.error = err.error?.error || 'Error al crear el equipo';
        this.loading = false;
      },
    });
  }

  updateTeam(): void {
    if (!this.team) return;
    this.teamService.updateTeam(this.team.id, {
      name: this.team.name,
      shield: this.team.shield,
    }).subscribe({
      next: () => {
        this.success = '✅ Cambios guardados';
        this.editMode = false;
      },
      error: () => (this.error = 'Error al actualizar equipo'),
    });
  }

  deleteTeam(): void {
    if (!this.team) return;
    if (!confirm('¿Seguro que quieres eliminar este equipo?')) return;
    this.teamService.deleteTeam(this.team.id).subscribe({
      next: () => {
        this.team = null;
        this.success = '🗑️ Equipo eliminado';
      },
      error: () => (this.error = 'Error al eliminar el equipo'),
    });
  }
}
