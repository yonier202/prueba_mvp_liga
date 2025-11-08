import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TeamListPage } from './team-list-page';

describe('TeamListPage', () => {
  let component: TeamListPage;
  let fixture: ComponentFixture<TeamListPage>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [TeamListPage]
    })
    .compileComponents();

    fixture = TestBed.createComponent(TeamListPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
