import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { AlertController, IonicModule, LoadingController, ToastController } from '@ionic/angular';
import { Game, Games } from '../../interfaces/game.interface';
import { ActivatedRoute, Router } from '@angular/router';
import { GameService } from '../../services/game.services';

@Component({
  selector: 'app-game-result',
  templateUrl: './game-result.page.html',
  styleUrls: ['./game-result.page.scss'],
  imports: [CommonModule, IonicModule, FormsModule]
})
export class GameResultPage implements OnInit {

  gameId!: number;
  game?: Game;
  homeScore!: number;
  awayScore!: number;

  constructor(
    private route: ActivatedRoute,
    private gameService: GameService,
    private loadingCtrl: LoadingController,
    private toastCtrl: ToastController,
    private router: Router,
    private alertCtrl: AlertController
  ) {}

  ngOnInit() {
    this.gameId = +this.route.snapshot.paramMap.get('id')!;
    this.loadGame();
  }

  loadGame() {
    // Obtener los partidos pendientes y buscar el que coincide con id
    this.gameService.getPendingGames().subscribe({
      next: (games) => {
        this.game = games.data.find(g => g.id === this.gameId);
        if (!this.game) {
          this.presentToast('Partido no encontrado o ya jugado');
          this.router.navigate(['/games/game-list']);
        }
      },
      error: (err) => {
        console.error(err);
        this.presentToast('Error al cargar el partido');
        this.router.navigate(['/games/game-list']);
      }
    });
  }

  async confirmAndSend() {
    if (this.homeScore === null || this.awayScore === null) {
      this.presentToast('Ingresa ambos marcadores');
      return;
    }

    const alert = await this.alertCtrl.create({
      header: 'Confirmar resultado',
      message: `${this.game?.home_team?.name || 'Local'} ${this.homeScore} - ${this.awayScore} ${this.game?.away_team?.name || 'Visitante'}`,
      buttons: [
        { text: 'Cancelar', role: 'cancel' },
        { text: 'Enviar', handler: () => this.sendResult() }
      ]
    });
    await alert.present();
  }

  async sendResult() {
    const loading = await this.loadingCtrl.create({ message: 'Enviando resultado...' });
    await loading.present();

    const payload = { home_score: this.homeScore, away_score: this.awayScore };

    this.gameService.reportResult(this.gameId, payload).subscribe({
      next: async (res) => {
        await loading.dismiss();
        this.presentToast('Resultado reportado correctamente');
        this.router.navigate(['/games/game-list']);
      },
      error: async (err) => {
        console.error(err);
        await loading.dismiss();
        this.presentToast('Error al reportar. Intenta de nuevo.');
      }
    });
  }

  async presentToast(msg: string) {
    const t = await this.toastCtrl.create({ message: msg, duration: 2000 });
    await t.present();
  }

}
