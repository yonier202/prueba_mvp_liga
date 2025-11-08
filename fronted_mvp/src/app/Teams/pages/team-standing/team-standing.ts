import { Component, OnInit } from '@angular/core';
import { TeamService } from '../../services/team.service';
import { CommonModule } from '@angular/common';
import { Team } from '../../interfaces/team-response.interface';

@Component({
  selector: 'app-team-standing',
  imports: [CommonModule],
  templateUrl: './team-standing.html',
  styleUrl: './team-standing.css',
})
export class TeamStanding implements OnInit {
  standings: any[] = [];

  constructor(private teamservice: TeamService) {}

  ngOnInit(): void {
    this.loadStandings();
  }

  loadStandings() {
    this.teamservice.getStandings().subscribe({
      next: (res) => {
        this.standings = res.data;
      },
      error: (err) => console.error(err)
    });
  }
}
