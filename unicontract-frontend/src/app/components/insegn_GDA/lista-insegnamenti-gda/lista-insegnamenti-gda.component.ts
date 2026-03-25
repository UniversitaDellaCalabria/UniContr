import { Router, ActivatedRoute } from '@angular/router';
import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { InsegnGDA } from './../../../classes/insegn-gda';
import { InsegnGDAService } from './../../../services/insegn-gda.service';
import { UniqueName } from './../../../shared/pipe/unique-name';
import { UniqueYear } from './../../../shared/pipe/unique-year';
import { BaseComponent } from 'src/app/shared/base-component/base.component';
import { MessageService } from 'src/app/shared/message.service';

@Component({
  selector: 'app-lista-insegnamenti-gda',
  templateUrl: './lista-insegnamenti-gda.component.html',
  styleUrls: ['./lista-insegnamenti-gda.component.css']
})
export class ListaInsegnamentiGDAComponent extends BaseComponent {

  constructor(private service: InsegnGDAService,
              private router: Router,
              private route: ActivatedRoute,
              messageService: MessageService) {
                super(messageService)
               }

  insegnGDA: InsegnGDA[] = [];
  filteredInsegn: InsegnGDA[] = [];

  private _searchTermTutor: string;

  isLoading = false;

  anno: string;

  selectedOption: string;
  numberOfItems: number;
  errorMessage: string;

  years: Array<Object> = [
    {year: '2023', name: '2023 / 2024'},
    {year: '2022', name: '2022 / 2023'},
    {year: '2021', name: '2021 / 2022'},
    {year: '2020', name: '2020 / 2021'},
    {year: '2019', name: '2019 / 2020'},
    {year: '2018', name: '2018 / 2019'},
    {year: '2017', name: '2017 / 2018'},
    {year: '2016', name: '2016 / 2017'},
  ];

  ngOnInit() {
    this.isLoading = false;
    this.messageService.clear();
    this.route.paramMap.subscribe(
      (param) => {
        const aa_off_id = param.get('aa_off_id');
        if (aa_off_id) {
          this.isLoading = true;
          this.service.getListaInsegnamentiGDA(aa_off_id).subscribe(
            response => this.insegnGDA = response['lista'],
            () => this.filteredInsegn = this.insegnGDA,
            () => this.isLoading = false
          );
        }
      }
    );
  }

  onChange(year) {
    if (year) {
      this.isLoading = true;
      this.service.getListaInsegnamentiGDA(year).subscribe(
        response => {
          this.searchTermTutor = null;
          this.insegnGDA = response['lista'];
        },
        () => this.filteredInsegn = this.insegnGDA,
        () => this.isLoading = false
      );
    }
  }

  get searchTermTutor(): string {
    return this._searchTermTutor;
  }

  set searchTermTutor(value: string) {
    this._searchTermTutor = value;
    this.filteredInsegn = this.filtraInsegnamentiTutor(value);
  }

  filtraInsegnamentiTutor(searchString: string) {
    return this.insegnGDA.filter(item => item.cod_fisc === searchString);
  }

  numRecord() {
    // this.numberOfItems = this.filteredInsegn.length;
    if (this.filteredInsegn.length === 1) {
      return 'È presente un insegnamento attribuito';
    } else if (this.filteredInsegn.length === 0) {
      return 'Non è presente alcun insegnamento';
    } else {
      return 'Sono presenti ' + this.filteredInsegn.length + ' insegnamenti attribuiti';
    }
  }

  aa(anno) {
    return +anno + 1;
  }

}
