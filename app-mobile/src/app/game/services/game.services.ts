import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { catchError, map, Observable, of, throwError } from 'rxjs';
import { Games } from '../interfaces/game.interface';

@Injectable({providedIn: 'root'})
export class GameService {

  private base = 'http://127.0.0.1:8000/api';

  constructor(private http: HttpClient) {}

  getPendingGames(): Observable<Games> {
    return this.http.get<Games>(`${this.base}/games`);
  }

  reportResult(gameId: number, payload: { home_score:number, away_score:number }): Observable<boolean>{
    return this.http.post(`${this.base}/games/${gameId}/result`, payload)
      .pipe(
        map(response => true),
        catchError(eror => of (false))
      );
  }
}
