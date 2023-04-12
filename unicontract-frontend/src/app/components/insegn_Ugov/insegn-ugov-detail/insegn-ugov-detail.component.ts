import { environment } from 'src/environments/environment';

import { NgForm } from '@angular/forms';
import { Component, OnInit, NgModule, LOCALE_ID, Input } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { MycurrencyPipe } from './../../../shared/pipe/custom.currencypipe';
import { InsegnamTools } from './../../../classes/insegnamTools';

import { InsegnUgov } from './../../../classes/insegn-ugov';
import { Insegnamento } from './../../../classes/insegnamento';
import { Precontrattuale } from './../../../classes/precontrattuale';
import { Docente } from './../../../classes/docente';
import { RuoloDocente } from './../../../classes/ruoloDocente';

import { InsegnUgovService } from './../../../services/insegn-ugov.service';
import { InsegnamentoService } from './../../../services/insegnamento.service';
import { PrecontrattualeService } from './../../../services/precontrattuale.service';
import { MessageService, BaseComponent } from './../../../shared';
import { DocenteService } from './../../../services/docente.service';
import { RuoloDocenteService } from './../../../services/ruoloDocente.service';
import { InsegnamentoInterface } from 'src/app/interface/insegnamento';
import { IPrecontrattuale } from 'src/app/interface/precontrattuale';
import { StoryProcess } from './../../../classes/storyProcess';
import { TitleCasePipe } from '@angular/common';

@Component({
  selector: 'app-insegn-ugov-detail',
  templateUrl: './insegn-ugov-detail.component.html',
  styleUrls: [
    './insegn-ugov-detail.component.css'
  ]
})

@NgModule({
  providers: [{
    provide: LOCALE_ID,
    useValue: 'it-IT' // for Italy,
  }]
})

export class InsegnUgovDetailComponent extends BaseComponent {

  private __ins: Insegnamento;

  @Input()
  set ins(ins: Insegnamento) {
    this.__ins = ins;
  }

  get ins() {
    return this.__ins;
  }

  item: InsegnUgov;

  tipo_atto_des_list: string[];
  tipo_emitt_des_list: string[];
  motivo_atto_cod_list: string[];
  numero_list: string[];
  data_list: string[];


  private precontr: Precontrattuale;
  private docente: Docente;
  private ruoloDocente: RuoloDocente;
  public modelid: number;
  checkTutor: boolean;
  checkIns: boolean;
  story: StoryProcess;

  constructor(private route: ActivatedRoute,
              private router: Router,
              private insegnUgovService: InsegnUgovService,
              private insegnamentoService: InsegnamentoService,
              private precontrattualeService: PrecontrattualeService,
              private docenteService: DocenteService,
              private ruoloDocenteService: RuoloDocenteService,
              messageService: MessageService,
              private tools: InsegnamTools) { super(messageService); }

  // tslint:disable-next-line:use-life-cycle-interface
  ngOnInit() {
    this.messageService.clear();
    this.route.paramMap.subscribe(
      (params) => {
        this.isLoading = true;
        this.insegnUgovService.getInsegnamentoUgov(+params.get('coper_id'), params.get('aa_off_id')).subscribe(
          response => {
              this.item = response['datiUgov'];
              this.tipo_atto_des_list = response['datiUgov']['tipo_atto_des'] ? response['datiUgov']['tipo_atto_des'].split('#') : "";
              this.tipo_emitt_des_list = response['datiUgov']['tipo_emitt_des'] ? response['datiUgov']['tipo_emitt_des'].split('#') : "";
              this.motivo_atto_cod_list = response['datiUgov']['motivo_atto_cod'] ? response['datiUgov']['motivo_atto_cod'].split('#') : "";
              this.numero_list = response['datiUgov']['numero'] ? response['datiUgov']['numero'].split('#') : "";
              this.data_list = response['datiUgov']['data'] ? response['datiUgov']['data'].split('#') : "";
            },
          (error) => this.handleError(error),
          () => this.complete()
        );
      }
    );
  }

  email(email: string, e_mail: string, e_mail_privata: string) {
    let domains = environment.valid_email_domains;
    for(let i=0;i<domains.length;i++){
        if (email.toLowerCase().endsWith(domains[i])) {
          return email;
        } else if (e_mail.toLowerCase().endsWith(domains[i])) {
          return e_mail;
        }
    }
    if (e_mail_privata !== '') {
        return e_mail_privata;
    }
    return '';
  }

  checkEmail(email: string, e_mail: string, e_mail_privata: string) {
    let domains = environment.valid_email_domains;
    for(let i=0;i<domains.length;i++){
        if (email !== null && email.toLowerCase().includes(domains[i])) {
            return true;
        } else if (e_mail !== null && e_mail.toLowerCase().includes(domains[i])) {
            return true;
        } else if (e_mail_privata !== null && e_mail_privata.toLowerCase().includes(domains[i])) {
            return true;
        }
    }
    return false;
  }

  datoMancante(value: string) {
    if (value === '') {
      return 'NULL';
    } else {
      return value;
    }
  }

  counterInsegnamenti(cod_ins, fiscal_code) {

    // copertura/{coper_id}/contatore/{cf}z
  }

  saveIns(data, nome: string, cognome: string, cf: string, id_ab: number, email: string) {
    if (data) {
      let tcp = new TitleCasePipe();
      const pre: IPrecontrattuale = {
        insegnamento: data,
        docente: {
          name: tcp.transform(nome) + ' ' + tcp.transform(cognome),
          nome: tcp.transform(nome),
          cognome: tcp.transform(cognome),
          cf: cf,
          email: email.toLowerCase(),
          v_ie_ru_personale_id_ab: id_ab,
          password: null,
        }
      };

      this.newPrecontrImportInsegnamento(pre);
    } else {
      this.messageService.error('Spiacente, nessun parametro passato');
    }
  }

  newPrecontrImportInsegnamento(pre: IPrecontrattuale) {
    this.isLoading= true;
    this.precontrattualeService.newPrecontrImportInsegnamento(pre).subscribe(
      response => {
        this.isLoading= false;
        if (response.success) {
          this.messageService.info('Insegnamento ' + pre.insegnamento.coper_id + ' inserito con successo');
          this.router.navigate(['home/detail-insegn', response.data.insegnamento.id]);
        } else {
          this.messageService.error(response.message);
        }
      }
    );
  }

  checkAttoData(data_ugov){
        if(!this.data_list) return false;
        if(!data_ugov) return false;
        let atto_precedente = false;
        let data_ugov_array = data_ugov.split("-");
        let new_data_ugov = data_ugov_array[2] + "-" + data_ugov_array[1] + "-" + data_ugov_array[0];
        this.data_list.forEach(function (data) {
            let d1 = new Date(data);
            let d2 = new Date(new_data_ugov);
            if(d1<=d2) atto_precedente = true;
        });
        return atto_precedente;
    }

  checkAttoTipo(){
        if(!this.tipo_atto_des_list) return false;
        let delibera_found = false;
        this.tipo_atto_des_list.forEach(function (data) {
            if(data == "Delibera") delibera_found = true;
        });
        return delibera_found;
    }



}
