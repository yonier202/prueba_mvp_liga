import { ComponentFixture, TestBed } from '@angular/core/testing';
import { GameResultPage } from './game-result.page';

describe('GameResultPage', () => {
  let component: GameResultPage;
  let fixture: ComponentFixture<GameResultPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(GameResultPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
