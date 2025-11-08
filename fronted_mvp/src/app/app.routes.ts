import { Routes } from '@angular/router';

export const routes: Routes = [
  {
    path: 'equipos',

    loadChildren: () => import('./Teams/teams.routes').then(m => m.routes)
  },
  {
    path: '**',
    pathMatch: 'full',
    redirectTo: 'equipos'
  }
];
