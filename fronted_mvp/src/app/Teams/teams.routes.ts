import { Routes } from '@angular/router';
import { TeamListPage } from './pages/team-list-page/team-list-page';
import { TeamFormPage } from './pages/team-form-page/team-form-page';
import { Layout } from './components/layout/layout';
import { TeamStanding } from './pages/team-standing/team-standing';


export const routes: Routes = [
  {
    path: '',
    component: Layout,
    children: [
      {
        path: 'list',
        component: TeamListPage
      },
      {
        path: 'new',
        component: TeamFormPage
      },
      {
        path: 'standing',
        component: TeamStanding
      },
      {
        path: '**',
        redirectTo: 'list'
      }]
  }
];
