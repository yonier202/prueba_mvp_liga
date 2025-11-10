import { Component, OnInit } from '@angular/core';
import { Team } from '../../../Teams/interfaces/team-response.interface';
import { TeamService } from '../../../Teams/services/team.service';
import { GameService } from '../../services/game.service';
import Swal from 'sweetalert2';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-game-form',
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './game-form.html',
  styleUrl: './game-form.css',
})
export class GameForm implements OnInit {
  teams: Team[] = [];
  home_team_id: number | null = null;
  away_team_id: number | null = null;

  constructor(
    private teamService: TeamService,
    private gameService: GameService
  ) {}

  ngOnInit(): void {
    this.loadTeams();
  }

  loadTeams() {
    this.teamService.getTeams().subscribe({
      next: res => this.teams = res.data,
      error: err => console.error(err)
    });
  }

  createGame() {
    if (!this.home_team_id || !this.away_team_id) {
      this.alerta('Campos incompletos', 'Debes seleccionar ambos equipos', 'warning');
      return;
    }

    if (this.home_team_id === this.away_team_id) {
      this.alerta('Selección inválida', 'No puedes seleccionar el mismo equipo dos veces', 'error');
      return;
    }

    this.gameService.createGame({
      home_team_id: this.home_team_id,
      away_team_id: this.away_team_id
    }).subscribe({
      next: success => {
        if (success) {
          this.alerta('Partido creado', 'El partido se ha creado correctamente', 'success');
          this.resetForm();
        } else {
          this.alerta('Error', 'No se pudo crear el partido', 'error');
        }
      },
      error: err => console.error(err)
    });
  }

  resetForm() {
    this.home_team_id = null;
    this.away_team_id = null;
  }

  alerta(title: string, msg: string, icon: any) {
    Swal.fire({
      title,
      text: msg,
      icon,
      confirmButtonText: 'Aceptar'
    });
  }
}
