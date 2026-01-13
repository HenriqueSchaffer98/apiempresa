import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FuncionarioList } from './funcionario-list';

describe('FuncionarioList', () => {
  let component: FuncionarioList;
  let fixture: ComponentFixture<FuncionarioList>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FuncionarioList]
    })
    .compileComponents();

    fixture = TestBed.createComponent(FuncionarioList);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
