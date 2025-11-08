import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TeamStanding } from './team-standing';

describe('TeamStanding', () => {
  let component: TeamStanding;
  let fixture: ComponentFixture<TeamStanding>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [TeamStanding]
    })
    .compileComponents();

    fixture = TestBed.createComponent(TeamStanding);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
