import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { catchError, map, Observable, of, tap } from 'rxjs';
import { Teams } from '../interfaces/team-response.interface';
import environment from '../../../environments/environment';

@Injectable({ providedIn: 'root' })
export class TeamService {
  private apiUrl = environment.API_URL;

  constructor(private http: HttpClient) {}

  getTeams(): Observable<Teams> {
    return this.http.get<Teams>(`${this.apiUrl}/teams`);
  }

  addTeam(team: { name: string }): Observable<boolean> {
    return this.http.post(`${this.apiUrl}/teams`, team)
      .pipe(
        //cualquier respuesta exitosa devuelve true
        map( res => true ),
        //cualquier error devuelve false
        catchError( err => of(false) )
      );
  }

  getStandings(): Observable<any> {
    return this.http.get(`${this.apiUrl}/standings`,)}
}
