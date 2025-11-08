export interface Teams {
  success: boolean;
  data:    Team[];
  message: string;
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
