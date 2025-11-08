import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { GameListPage } from './pages/game-list/game-list.page';
import { GameResultPage } from './pages/game-result/game-result.page';

const routes: Routes = [
  {
    path: '',
    redirectTo: 'game-list',
    pathMatch: 'full'
  },
  {
    path: 'game-list',
    component: GameListPage,
  },
  {
    path: 'game-result/:id',
    component: GameResultPage,
  }

];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class GamePageRoutingModule {}
