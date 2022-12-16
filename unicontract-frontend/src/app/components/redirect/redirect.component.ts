import { Component, OnInit } from "@angular/core";

@Component({
  selector: "redirect",
  templateUrl: "redirect.component.html"
})

export class Redirect_FAQ implements OnInit {
  constructor() {}

  ngOnInit() {
    window.location.href = 'https://docs.google.com/document/d/1UioNHVdc1lIAP1okcrhjR6CXKtzHuhVBIhg26Js6yqg/edit?usp=sharing';
  }
}

export class Redirect_FAQ_operatori implements OnInit {
  constructor() {}

  ngOnInit() {
    window.location.href = 'https://docs.google.com/document/d/1cfT7GRT3ThzNMJ3SXvHsJYSKG-UCVAnlu8RWvGsWwCs/edit?usp=sharing';
  }
}

export class Redirect_Supporto_compilazione implements OnInit {
  constructor() {}

  ngOnInit() {
    window.location.href = 'https://ticket.unical.it/tickets/new/17/443/';
  }
}

export class Redirect_Supporto_tecnico implements OnInit {
  constructor() {}

  ngOnInit() {
    window.location.href = 'https://ticket.unical.it/tickets/new/17/444/';
  }
}
