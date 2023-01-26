import { Component, OnInit } from '@angular/core';
import { TranslateService } from '@ngx-translate/core';
import { BaseComponent } from './../../../shared/base-component/base.component';
import { ActivatedRoute, Router } from '@angular/router';
import { MessageService } from './../../../shared';
import { InsegnamTools } from './../../../classes/insegnamTools';
import { PrecontrattualeService } from './../../../services/precontrattuale.service';
import { SummaryService } from './../../../services/quadro-riepilogativo.service';
import { RouteMetods } from './../../../classes/routeMetods';
import { encode, decode } from 'base64-arraybuffer';

import { FormGroup } from '@angular/forms';
import { FormlyFormOptions, FormlyFieldConfig } from '@ngx-formly/core';
import { IPrecontrStore } from 'src/app/interface/precontrattuale';
import { ConfirmationDialogService } from 'src/app/shared/confirmation-dialog/confirmation-dialog.service';
import { B1ConflittoService } from 'src/app/services/b1conflitto.service';
import { EmailListService } from 'src/app/services/emailList.service';
import { SendEmail } from './../../../classes/sendEmail';
import { THIS_EXPR } from '@angular/compiler/src/output/output_ast';
import { notEqual } from 'assert';
import { StoryProcessService } from './../../../services/storyProcess.service';
import { StoryProcess } from './../../../classes/storyProcess';
import { stringify } from 'querystring';
import { NgxPermissionsService } from 'ngx-permissions';
import { SessionStorageService } from 'src/app/services/session-storage.service';

@Component({
  selector: 'app-quadro-riepilogativo',
  templateUrl: './quadro-riepilogativo.component.html',
  styleUrls: ['./quadro-riepilogativo.component.css']
})

export class QuadroRiepilogativoComponent extends BaseComponent {

  email: SendEmail = null;
  gestioneannulamento: boolean = false;
  gestioneinformazioni: boolean = false;
  toggle_confl_int_dip: boolean = false;
  story: StoryProcess;
  datiCont: any = null;

  _visualizzaContratto: boolean = false;
  get visualizzatoContratto() : boolean {
    return this._visualizzaContratto || this.sessionStorageService.getItem('visualizzaContratto'+((this.items) ? this.items.id : ''));
  }

  set visualizzatoContratto(value: boolean) {
    this._visualizzaContratto = value;
    this.sessionStorageService.setItem('visualizzaContratto'+this.items.id, value);
  }

  items: any = null;
  idins: number;

  form = new FormGroup({});
  model: any = {
    attachments: [
      {
        attachmenttype_codice: 'CONFL_INT_DIP',
      },

    ]
  };
  options: FormlyFormOptions = {
    formState: {
      model: this.model,
      isLoading: this.isLoading,
    },
  };

  fields: FormlyFieldConfig[] = [
    // motivazione
    {
      fieldGroupClassName: 'row',
      fieldGroup: [
        {
          key: 'motivazione',
          type: 'textarea',
          className: 'col-md-12',
          templateOptions: {
            required: true,
            label: 'Motivazione',
            rows: 5,
            maxLength: 450,
            description: 'Lunghezza massima 450 caratteri',
          },

          expressionProperties: {
            'templateOptions.description': (model, formState) => {
              if (model.tipo_annullamento=='REVOC') {
                return 'Specificare il numero di decreto della revoca (lunghezza massima 450 caratteri)';
              }
              return 'Lunghezza massima 450 caratteri';
            },

          }
        },
      ],
    }
  ];

  fields2: FormlyFieldConfig[] = [
    // motivazione
    {
      fieldGroupClassName: 'row',
      fieldGroup: [
        {
          key: 'corpo_testo',
          type: 'textarea',
          className: 'col-md-12',
          templateOptions: {
            required: true,
            label: 'Testo della richiesta',
            rows: 5,
          },
        },
      ],
    }
  ];

  fields3: FormlyFieldConfig[] = [
    // upload conflitto interessi dipartimento
    {
        fieldGroup: [
        {
          key: 'attachments',
          type: 'repeat',
          templateOptions: {
            translate: true,
            label: 'a1_label19',
            min: 0,
            max: 2,
            btnHidden: true,
            btnRemoveHidden: true,
          },
          fieldArray: {
            fieldGroup: [
              {
                fieldGroupClassName: 'row',
                fieldGroup: [
                // attachmenttype_codice
                {
                  key: 'attachmenttype_codice',
                  type: 'input',
                  defaultValue: 'CONFLINT_DIP',
                  templateOptions: {
                    type: 'hidden',
                    options: [
                      { codice: 'CONFL_INT_DIP', descrizione: 'Dichiarazione conflitto interessi Dipartimento' },
                    ],
                    valueProp: 'codice',
                    labelProp: 'descrizione',
                  }
                },
                // filename
                {
                  // NB è stato richiesto in fase di validazione di poter inserire dei
                  // riferimenti ad degli allegati ma senza includere il file
                  key: 'filename',
                  type: 'fileinput',
                  className: 'col-md-6',
                  validation: {
                    show: true
                  },
                  templateOptions: {
                    label: 'Dichiarazione conflitto interessi Dipartimento',
                    type: 'input',
                    readonly: true,
                    placeholder: 'Carica il documento . . . ',
                    description: 'N.B. Il documento deve essere in formato PDF e PRIVO DI DATI SENSIBILI. Dimensione massima 2MB.',
                    accept: 'application/pdf',
                    maxLength: 255,
                    required: true,
                    onSelected: (selFile, field) => { this.onSelectCurrentFile(selFile, field); }
                  },
                  validators: {
                    maxsize: {
                      expression: (c,f) => (f.model._filesize && f.model._filesize > 2720000) ? false : true,
                      message: (error, field) => `La dimensione del file eccede la dimensione massima consentita `,
                    },
                    filetype: {
                      expression: (c,f) => (c.value ? (c.value.endsWith('.pdf') ? true : false) :true),
                      message: (error, field) => `Il formato file richiesto è PDF`,
                    }

                  },
                },

                // bottoni azione
                {
                  fieldGroupClassName: 'btn-toolbar',
                  className: 'col-md-2 btn-group',
                  fieldGroup: [
                    {
                      type: 'button',
                      className: 'mt-4 pt-2',
                      templateOptions: {
                        btnType: 'primary oi oi-data-transfer-download',
                        title: 'Scarica il documento',
                        // icon: 'oi oi-data-transfer-download'
                        onClick: ($event, model, field) => this.download(model.id),
                      },
                      hideExpression: (model: any, formState: any) => {
                        return !model.id;
                      },
                    },
                  ],
                },
              ],
              },
              {
                fieldGroupClassName: 'row',
                fieldGroup: [
                  {
                    key: 'filevalue',
                    type: 'input',
                    templateOptions: {
                      type: 'hidden'
                    },
                  },
                  {
                    key: 'id',
                    type: 'input',
                    templateOptions: {
                      type: 'hidden'
                    },
                  },
                ],
              },
            ],
          },
        },
      ]
    }
  ];



  constructor(private route: ActivatedRoute,
              private router: Router,
              public messageService: MessageService,
              private summaryService: SummaryService,
              private b1ConflittolService: B1ConflittoService,
              protected translateService: TranslateService,
              protected confirmationDialogService: ConfirmationDialogService,
              private emailService: EmailListService,
              private tools: InsegnamTools,
              private storyService: StoryProcessService,
              private permissionsService: NgxPermissionsService,
              private sessionStorageService: SessionStorageService,
              private goto: RouteMetods) { super(messageService); }

  // tslint:disable-next-line:use-life-cycle-interface
  ngOnInit() {
    this.route.paramMap.subscribe(
      (params) => {
        this.isLoading = true;
        this.summaryService.getSummary(+params.get('id')).subscribe(
          response => {
            this.items = response['datiGenerali'];
            this.idins = +params.get('id');

            this.permissionsService.hasPermission(['OP_APPROVAZIONE_AMM',
                                                   'OP_APPROVAZIONE_ECONOMICA',
                                                   'SUPER-ADMIN']).then(
              (hasPermission) => hasPermission ? this.getIddg(this.items.coper_id) : {}
            );

            if (!this.items['attachments'] || this.items['attachments'].length === 0) {
                // se non ci sono allegati
                this.items['attachments'] =  [
                  {
                    attachmenttype_codice: 'CONFL_INT_DIP',
                  },
                ];
              }

          },
          (error) => this.handleError(error),
          () => this.complete()
        );
      }
    );
  }

  getOrdinativi(compenso: any) {
    return compenso.ordinativi.map(x=>x.id_dg).join(', ');
  }

  getIddg(coper_id) {
    this.summaryService.getIddg(+coper_id).subscribe(
      response => {
        this.datiCont = response['datiCont'];
      },
      (error) => this.handleError(error),
      () => this.complete()
    );
  }

  isRevisioneAmministrativa() {
    if (this.items.current_place === 'revisione_amministrativa' || this.items.current_place === 'revisione_amministrativaeconomica') {
      return true;
    }
    return false;
  }

  isRevisioneEconomica() {
    if (this.items.current_place === 'revisione_economica' || this.items.current_place === 'revisione_amministrativaeconomica_economica') {
      return true;
    }
    return false;
  }

  summaryTxt5_amministrativa() {
    if (this.isRevisioneAmministrativa()) {
      return this.translateService.instant('summary_txt5_revisione');
    }
    if (!this.items.flag_confl_int_dip){
        return this.translateService.instant('summary_txt5_extra');
    }
    return this.translateService.instant('summary_txt5');
  }

  summaryTxt5_economica() {
    // 'revisione_economica'
    // 'revisione_amministrativaeconomica_economica'
    if (this.items.current_place === 'revisione_economica') {
      return this.translateService.instant('summary_txt5_revisione');
    }
    if (this.items.current_place === 'revisione_amministrativaeconomica_economica') {
      return this.translateService.instant('summary_txt5_revisione2');
    }
    return this.translateService.instant('summary_txt5');
  }

  previewcontratto() {
    this.isLoading = true;

    let newWindow = window.open();//OPEN WINDOW FIRST ON SUBMIT THEN POPULATE PDF
    newWindow.document.write('caricamento ...');

    this.summaryService.previewContratto(this.idins).subscribe(file => {
      this.isLoading = false;
      if (file && file.filevalue) {
        this.visualizzatoContratto = true;

        const blob = new Blob([decode(file.filevalue)], { type: 'application/pdf' });

        const fileURL = URL.createObjectURL(blob);
        newWindow.location.href = fileURL;//POPULATING PDF
      }else{
        newWindow.close();
      }
    },
    e => {
      this.isLoading = false;
      newWindow.close();
      console.log(e);
    }
    );
  }

  downloadContratto(event) {
    if (this.items.flag_submit) {
      const attach = this.items['attachments'].find(x => x.attachmenttype_codice === 'CONTR_BOZZA');
      this.download(attach.id);
    }
  }

  downloadContrattoFirma() {
    if (this.items.flag_submit) {
      const attach = this.items['attachments'].find(x => x.attachmenttype_codice === 'CONTR_FIRMA');
      this.download(attach.id);
    }
  }

  downloadCV() {
    if (this.items.flag_submit) {
      const attach = this.items['userattachments'].find(x => x.attachmenttype_codice === 'DOC_CV');
      if (attach){
        this.download(attach.id);
      }else {
        this.messageService.error('Allegato non presente');
      }
    }
  }

  downloadConflIntDip() {
    if (this.items.flag_submit) {
      const attach = this.items['attachments'].find(x => x.attachmenttype_codice === 'CONFL_INT_DIP');
      if (attach){
        this.download(attach.id);
      }else {
        this.messageService.error('Allegato non presente');
      }
    }
  }

  download(id) {
    this.summaryService.download(id).subscribe(file => {
      if (file.filevalue) {
        const blob = new Blob([decode(file.filevalue)]);
        saveAs(blob, file.filename);
      }
    },
      e => { console.log(e); }
    );

  }

  downloadModulisticaPrecontr(){
    this.isLoading = true;
     //aperture al di fuori della chiamata asicrona per il blocco popup
    let newWindow = window.open();//OPEN WINDOW FIRST ON SUBMIT THEN POPULATE PDF
    newWindow.document.write('caricamento ...');

    this.summaryService.modulisticaPrecontr(this.idins).subscribe(file => {
      this.isLoading = false;
      if (file && file.filevalue) {
        const blob = new Blob([decode(file.filevalue)], { type: 'application/pdf' });
        const fileURL = URL.createObjectURL(blob);
        newWindow.location.href = fileURL;//POPULATING PDF
      }else{
        newWindow.close();
      }
    },
    e => {
      this.isLoading = false;
      newWindow.close();
      console.log(e);
    }
    );
  }

  generatePdf(item, kind) {
    // implementare api
    this.b1ConflittolService.generatePdf(item.b1_confl_interessi_id, kind).subscribe(file => {
        if (file.filevalue) {
          const blob = new Blob([decode(file.filevalue)]);
          saveAs(blob, file.filename);
        }
      },
      e => { console.log(e); }
    );
  }

  validazioneAmm() {
    this.confirmationDialogService.confirm('Conferma', 'Procedere con l\'operazione di validazione?')
    .then((confirmed) => {
      if (confirmed) {
        const data: IPrecontrStore<any> = {
          insegn_id: this.idins,
          entity: {
            flag_upd: true
          }
        };

        this.isLoading = true;
        this.summaryService.validazioneAmm(data).subscribe(
          response => {
            this.isLoading = false;
            if (response.success) {
              this.items = {...this.items, ...response.data};
              this.messageService.info('Operazione di validazione terminata con successo');
              this.storyProcess('Precontrattuale validata da DRU – Area Professori e Ricercatori');
            } else {
              this.messageService.error(response.message);
            }
          }
        );
      }
    });
  }

  validazioneEconomica() {
    this.confirmationDialogService.confirm('Conferma', 'Procedere con l\'operazione di validazione?')
    .then((confirmed) => {
      if (confirmed) {
        const data: IPrecontrStore<any> = {
          insegn_id: this.idins,
          entity: {
            flag_amm: true
          }
        };

        this.isLoading = true;
        this.summaryService.validazioneEconomica(data).subscribe(
          response => {
            this.isLoading = false;
            if (response.success) {
              this.items = {...this.items, ...response.data};
              this.messageService.info('Operazione di validazione terminata con successo');
              if (response.message){
                this.messageService.info(response.message,false);
              }
              this.storyProcess('Precontrattuale validata da Ufficio Trattamenti Economici e Previdenziali');
            } else {
              this.messageService.error(response.message);
            }
          }
        );
      }
    });
  }

  annullaAmm() {
    this.confirmationDialogService.inputConfirm('Conferma', 'Procedere con l\'operazione di annullamento?')
    .then((confirmed) => {
      if (confirmed.result) {
        const data: IPrecontrStore<any> = {
          insegn_id: this.idins,
          entity: {
            flag_upd: false,
            note: confirmed.entity
          }
        };

        this.isLoading = true;
        this.summaryService.annullaAmm(data).subscribe(
          response => {
            this.isLoading = false;
            if (response.success) {
              this.items = {...this.items, ...response.data};
              this.messageService.info('Operazione di annullamento terminata con successo');
            } else {
              this.messageService.error(response.message);
            }
          }
        );
      }
    });
  }

  annullaEconomica() {
    this.confirmationDialogService.inputConfirm('Conferma', 'Procedere con l\'operazione di annullamento?')
    .then((confirmed) => {
      if (confirmed.result) {
        const data: IPrecontrStore<any> = {
          insegn_id: this.idins,
          entity: {
            flag_amm: false,
            note: confirmed.entity
          }
        };

        this.isLoading = true;
        this.summaryService.annullaEconomica(data).subscribe(
          response => {
            this.isLoading = false;
            if (response.success) {
              this.items = {...this.items, ...response.data};
              this.messageService.info('Operazione di annullamento terminata con successo');
            } else {
              this.messageService.error(response.message);
            }
          }
        );
      }
    });
  }

  presaVisione() {
    const message: string =
    `<p align="justify">Procedendo con questa operazione, si dichiara di aver preso visione
    del contratto di insegnamento e di averlo accettato in tutte le sue parti.
    <br>
    Grazie per il suo contributo! </p>`;

    this.confirmationDialogService.confirm('Accettazione contratto', null, 'Accetta', 'Annulla', 'lg', message)
    .then((confirmed) => {
      if (confirmed) {
        const data: IPrecontrStore<any> = {
          insegn_id: this.idins,
          entity: {
            flag_accept: true,
          }
        };

        this.isLoading = true;
        this.summaryService.presaVisioneAccettazione(data).subscribe(
          response => {
            this.isLoading = false;
            if (response.success) {
              this.items = {...this.items, ...response.data.validazioni};
              this.items.attachments = response.data.attachments;
              this.sessionStorageService.removeItem('visualizzaContratto'+this.items.id);
              this.messageService.info('Operazione di presa visione e accettazione terminata con successo');
              this.storyProcess('Presa visione e accettazione del contratto da parte del docente');
            } else {
              this.messageService.error(response.message);
            }
          }
        );
      }
    });
  }

  annullaContratto() {
    const msg = 'Procedere con l\'operazione di ' + (this.model.tipo_annullamento=='REVOC' ? 'revoca?' : 'rinuncia?');
    this.confirmationDialogService.confirm('Conferma', msg)
    .then((confirmed) => {
      if (confirmed) {
        const data: IPrecontrStore<any> = {
          insegn_id: this.idins,
          entity: this.model
        };

        this.isLoading = true;
        this.summaryService.annullaContratto(data).subscribe(
          response => {
            this.isLoading = false;
            if (response.success) {
              this.items = {...this.items, ...response.data};
              this.messageService.info('Operazione di '+(this.model.tipo_annullamento=='REVOC' ? 'revoca' : 'rinuncia')+' terminata con successo');
            } else {
              this.messageService.error(response.message);
            }
          }
        );
      }
    });
  }

  rinunciaCompenso() {
    this.confirmationDialogService.confirm('Conferma', 'Procedere con l\'operazione di rinuncia?')
    .then((confirmed) => {
      if (confirmed) {
        const data: IPrecontrStore<any> = {
          insegn_id: this.idins,
          entity: {
            flag_no_compenso: true
          }
        };

        this.isLoading = true;
        this.summaryService.rinunciaCompenso(data).subscribe(
          response => {
            this.isLoading = false;
            if (response.success) {
              this.items = {...this.items, ...response.data};
              this.messageService.info('Operazione di rinuncia al compenso terminata con successo');
            } else {
              this.messageService.error(response.message);
            }
          }
        );
      }
    });
  }

  annullaRinuncia() {
    this.confirmationDialogService.confirm('Conferma', 'Procedere con l\'operazione di annullamento?')
    .then((confirmed) => {
      if (confirmed) {
        const data: IPrecontrStore<any> = {
          insegn_id: this.idins,
          entity: {
            flag_no_compenso: false
          }
        };

        this.isLoading = true;
        this.summaryService.annullaRinuncia(data).subscribe(
          response => {
            this.isLoading = false;
            if (response.success) {
              this.items = {...this.items, ...response.data};
              this.messageService.info('Operazione di annullamento terminata con successo');
            } else {
              this.messageService.error(response.message);
            }
          }
        );
      }
    });
  }

  isRinuncia() {
    return this.items.flag_no_compenso;
  }

  // stati
  isCompilato() {
    return this.items.flag_submit === 1;
  }

  isValidatoAmm() {
    return this.items.flag_upd === 1 && this.isCompilato();
  }

  isValidatoEconomica() {
    return this.items.flag_amm === 1 && this.isValidatoAmm();
  }

  isAccettato() {
    return this.items.flag_accept === 1 && this.isValidatoEconomica();
  }

  isFirmato() {
    return (this.items.stato === 1 || this.items.stato === 3) && this.isAccettato();
  }

  isAnnullato() {
    return this.items.stato === 2 || this.items.stato === 3;
  }

  // step attivo compilazionie
  isStepCompilazione() {
    return !this.isCompilato();
  }

  // step attivo per eseguire la prima validazione
  isStepValidazioneAmm() {
    return this.items.flag_upd === 0 && this.isCompilato();
  }

  // step attivo per eseguire la seconda validazione
  isStepValidazioneEconomica() {
    return this.items.flag_amm === 0 && this.isValidatoAmm();
  }

  isStepPresaVisione() {
    return this.items.flag_accept === 0 && this.isValidatoEconomica();
  }

  isAnnullabile() {
    return this.items.stato < 1;
  }


  toggleGestioneannulamento(tipo) {

    //se è gia aperto e il tipo è diverso dal corrente
    if (this.gestioneannulamento && this.model.tipo_annullamento && this.model.tipo_annullamento != tipo){
      this.model.tipo_annullamento = tipo;
    }else{
      this.model.tipo_annullamento = tipo;
      this.gestioneannulamento = !this.gestioneannulamento;
    }
  }

  toggleGestioneInformazioni() {
    this.gestioneinformazioni = !this.gestioneinformazioni;
  }

  sendInfoEmail() {
    // tslint:disable-next-line:max-line-length
    this.confirmationDialogService.confirm('Conferma', 'Vuoi procedere con l\'invio della richiesta di modifica o integrazioni della modulistica precontrattuale?' )
    .then((confirmed) => {
      if (confirmed) {
        this.isLoading = true;
        const data: IPrecontrStore<any> = {
          insegn_id: this.idins,
          entity: this.model
        };
        this.summaryService.sendInfoEmail(data).subscribe(
          response => {
            if (response.success) {
              this.items['richiesta'] = response.data;
              this.gestioneinformazioni = false;
              this.messageService.info(response.message);
              this.storyProcess('Invio richiesta modifica/integrazioni al docente');
            } else {
              this.messageService.error(response.message);
            }
          },
          (error) => this.isLoading = false,
          () => this.isLoading = false
        );
      }
    });
  }

  storyProcess(description: string) {
    this.story = new StoryProcess();
    this.story.insegn_id = this.idins;
    this.story.descrizione = description;
    this.storyService.newStory(this.story).subscribe(
      response => {
        if (response['success']) {
          this.messageService.info('Storia del processo aggiornata con successo', false);
        } else {
          this.messageService.error(response['message']);
        }
      }
    );
  }

  getUgovIddg(coper_id) {
    // let iddg = null;
    this.summaryService.getIddg(coper_id).subscribe(
      response => {
        return response['datiCont']['id_dg'];
      },
      (error) => this.handleError(error),
      () => this.complete()
    );
  }


  toggleUploadConflIntDip() {
    //se è gia aperto, lo chiudi
    this.toggle_confl_int_dip = !this.toggle_confl_int_dip;
  }


    onSelectCurrentFile(currentSelFile, field: FormlyFieldConfig) {
        const currentAttachment = field.formControl.parent.value;
        if (currentSelFile == null) {
          // caso di cancellazione
          field.model._filesize = null;
          currentAttachment.filevalue = null;
          return;
        }
        field.model._filesize = currentSelFile.size;
        field.formControl.updateValueAndValidity();

        this.isLoading = true;
        currentAttachment.model_type = 'precontruattuale';

        const reader = new FileReader();

        reader.onload = async (e: any) => {
          this.isLoading = true;
          field.formControl.parent.get('filevalue').setValue(encode(e.target.result));
          if (currentSelFile.name.search('pdf') > 0) {
            try {
              field.formControl.markAsDirty();
            } catch (error) {
              console.log(error);
              this.isLoading = false;
            }
          }

          if (!currentAttachment.filevalue) {
            this.isLoading = false;
            return;
          }
          this.isLoading = false;
        };
        reader.readAsArrayBuffer(currentSelFile);
      }


    uploadConflIntDip() {
        this.confirmationDialogService.confirm('Conferma', 'Procedere con l\'operazione di upload?')
        .then((confirmed) => {
          if (confirmed) {
            const data = {
              insegn_id: this.idins,
              attachments: this.form.value.attachments,
            };
            this.isLoading = true;
            this.summaryService.uploadConflIntDip(data).subscribe(
              response => {
                this.isLoading = false;
                if (response.success) {
                  this.items = {...this.items, ...response.data};
                  this.items['flag_confl_int_dip'] = 1;
                  this.messageService.info('Operazione di upload completata con successo');
                } else {
                  this.messageService.error(response.message);
                }
              }
            );
          }
        });
      }
}
