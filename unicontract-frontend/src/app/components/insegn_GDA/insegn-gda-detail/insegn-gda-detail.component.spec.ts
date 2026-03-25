import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { InsegnGDADetailComponent } from './insegn-gda-detail.component';

describe('InsegnGDADetailComponent', () => {
  let component: InsegnGDADetailComponent;
  let fixture: ComponentFixture<InsegnGDADetailComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ InsegnGDADetailComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(InsegnGDADetailComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
