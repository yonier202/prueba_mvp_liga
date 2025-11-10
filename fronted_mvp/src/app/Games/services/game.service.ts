import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { catchError, map, Observable, of } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class GameService {
  private apiUrl = 'http://127.0.0.1:8000/api';

  constructor(private http: HttpClient) {}

  createGame(game: { home_team_id: number; away_team_id: number }): Observable<boolean> {
    return this.http.post(`${this.apiUrl}/games`, game).pipe(
      map(() => true),
      catchError(() => of(false))
    );
  }
}
