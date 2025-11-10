import { Routes } from '@angular/router';
import { Layout } from '../Teams/components/layout/layout';
import { GameForm } from './pages/game-form/game-form';


export const routes: Routes = [
  {
    path: '',
    component: Layout,
    children: [
      {
        path: 'create',
        component: GameForm
      },
      {
        path: '**',
        redirectTo: 'list'
      }]
  }
];
