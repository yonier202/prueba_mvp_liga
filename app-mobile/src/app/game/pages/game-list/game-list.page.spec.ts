import { ComponentFixture, TestBed } from '@angular/core/testing';
import { GameListPage } from './game-list.page';

describe('GameListPage', () => {
  let component: GameListPage;
  let fixture: ComponentFixture<GameListPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(GameListPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
