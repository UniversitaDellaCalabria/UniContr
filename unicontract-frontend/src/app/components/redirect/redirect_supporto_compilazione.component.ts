import { Component, OnInit } from "@angular/core";

@Component({
  selector: "redirect_supporto_compilazione",
  templateUrl: "redirect.component.html"
})


export class Redirect_Supporto_compilazione implements OnInit {
  constructor() {}

  ngOnInit() {
    window.location.href = 'https://ticket.unical.it/tickets/new/17/443/';
  }
}
