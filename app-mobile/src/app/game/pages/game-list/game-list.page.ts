import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { IonicModule, LoadingController, ToastController } from '@ionic/angular';
import { Game } from '../../interfaces/game.interface';
import { GameService } from '../../services/game.services';
import { Router } from '@angular/router';

@Component({
  selector: 'app-game-list',
  templateUrl: './game-list.page.html',
  styleUrls: ['./game-list.page.scss'],
  imports: [CommonModule, IonicModule, FormsModule],
})
export class GameListPage implements OnInit {
  games: Game[] = [];
  loading: HTMLIonLoadingElement | null = null;

  constructor(
    private gameService: GameService,
    private router: Router,
    private loadingCtrl: LoadingController,
    private toastCtrl: ToastController
  ) {}

  async ngOnInit() {
    await this.loadGames();
  }

  async loadGames() {
    this.loading = await this.loadingCtrl.create({ message: 'Cargando partidos...' });
    await this.loading.present();

    this.gameService.getPendingGames().subscribe({
      next: async (res) => {
        this.games = res.data;
        await this.loading?.dismiss();
      },
      error: async (err) => {
        console.error(err);
        await this.loading?.dismiss();
        const t = await this.toastCtrl.create({ message: 'Error cargando partidos', duration: 2000 });
        await t.present();
      }
    });
  }

  goToReport(game: Game) {
    this.router.navigate(['/games/game-result', game.id]);
  }

}
