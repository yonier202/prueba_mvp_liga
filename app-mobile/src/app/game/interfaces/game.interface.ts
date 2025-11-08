export interface Games {
  success: boolean;
  data:    Game[];
  message: string;
}

export interface Game {
  id:           number;
  home_team_id: number;
  away_team_id: number;
  home_score:   null;
  away_score:   null;
  status:       string;
  created_at:   Date;
  updated_at:   Date;
  home_team:    Team;
  away_team:    Team;
}

export interface Team {
  id:         number;
  name:       string;
  played:     number;
  won:        number;
  drawn:      number;
  lost:       number;
  points:     number;
  created_at: Date;
  updated_at: Date;
}

