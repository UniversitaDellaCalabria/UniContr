import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { InsegnamentoGDAComponent } from './insegnamento-gda.component';

describe('InsegnamentoGDAComponent', () => {
  let component: InsegnamentoGDAComponent;
  let fixture: ComponentFixture<InsegnamentoGDAComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ InsegnamentoGDAComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(InsegnamentoGDAComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
