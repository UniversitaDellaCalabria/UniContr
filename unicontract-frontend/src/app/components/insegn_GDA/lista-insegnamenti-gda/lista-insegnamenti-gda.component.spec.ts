import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListaInsegnamentiGDAComponent } from './lista-insegnamenti-gda.component';

describe('ListaInsegnamentiGDAComponent', () => {
  let component: ListaInsegnamentiGDAComponent;
  let fixture: ComponentFixture<ListaInsegnamentiGDAComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListaInsegnamentiGDAComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListaInsegnamentiGDAComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
