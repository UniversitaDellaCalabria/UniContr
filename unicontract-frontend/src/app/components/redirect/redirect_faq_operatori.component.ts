import { Component, OnInit } from "@angular/core";

@Component({
  selector: "redirect",
  templateUrl: "redirect.component.html"
})


export class Redirect_FAQ_operatori implements OnInit {
  constructor() {}

  ngOnInit() {
    window.location.href = 'https://docs.google.com/document/d/1cfT7GRT3ThzNMJ3SXvHsJYSKG-UCVAnlu8RWvGsWwCs/edit?usp=sharing';
  }
}
