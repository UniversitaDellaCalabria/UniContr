import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { Router } from '@angular/router';
import { InsegnGDA } from './../../../classes/insegn-gda';
import { InsegnGDAService } from './../../../services/insegn-gda.service';
import { InsegnamTools } from './../../../classes/insegnamTools';

@Component({
  // tslint:disable-next-line:component-selector
  selector: 'tr[app-insegnamento-gda]',
  templateUrl: './insegnamento-gda.component.html',
  styleUrls: ['./insegnamento-gda.component.css']
})
export class InsegnamentoGDAComponent implements OnInit {

  // tslint:disable-next-line:no-input-rename
  @Input('insegn-gda') gda: InsegnGDA;

  constructor(private insegnGDAService: InsegnGDAService,
              private route: Router,
              public tools: InsegnamTools) { }

  ngOnInit() {
  }

  showInsegnGDADetail() {
    this.route.navigate(['home/gda-insegn-detail', this.gda.coper_id, this.gda.aa_off_id]);
  }

}
