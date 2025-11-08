import { Component, OnInit } from '@angular/core';
import { TeamService } from '../../services/team.service';
import { CommonModule } from '@angular/common';
import {RouterModule} from '@angular/router';
import { Team } from '../../interfaces/team-response.interface';

@Component({
  selector: 'app-team-list-page',
  imports: [CommonModule, RouterModule],
  templateUrl: './team-list-page.html',
  styleUrl: './team-list-page.css',
})
export class TeamListPage implements OnInit {
  teams: Team[] = [];

  constructor(private teamService: TeamService) {}

  ngOnInit(): void {
    this.loadTeams();
  }

  loadTeams() {
    this.teamService.getTeams().subscribe({
      next: (res) => {
        this.teams = res.data;
      },
      error: (err) => console.error(err)
    });
  }
}
