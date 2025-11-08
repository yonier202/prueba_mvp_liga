import { Component } from '@angular/core';
import { TeamService } from '../../services/team.service';
import { Router, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-team-form-page',
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './team-form-page.html',
  styleUrl: './team-form-page.css',
})
export class TeamFormPage {
   name = '';

  constructor(
    private teamService: TeamService,
    private router: Router
  ) {}

  createTeam() {
    if (!this.name.trim()) return;
    this.teamService.addTeam({ name: this.name }).subscribe({
      next: () => {
        this.alerta('Equipo creado', 'El equipo se ha creado correctamente', 'success', 'Aceptar');
        this.router.navigate(['/teams']);
      },
      error: (err) =>
        this.alerta('Error', `No se pudo crear el equipo: ${err.message}`, 'error', 'Aceptar')
    });
  }

  alerta($title: string, $msg: string, $icon: any, $confirmButtonText: string) {
    Swal.fire({
      title: $title,
      text: $msg,
      icon: $icon,
      confirmButtonText: $confirmButtonText
    });
  }
}
