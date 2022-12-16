import { Component, OnInit } from "@angular/core";

@Component({
  selector: "redirect_faq",
  templateUrl: "redirect.component.html"
})

export class Redirect_FAQ implements OnInit {
  constructor() {}

  ngOnInit() {
    window.location.href = 'https://docs.google.com/document/d/1UioNHVdc1lIAP1okcrhjR6CXKtzHuhVBIhg26Js6yqg/edit?usp=sharing';
  }
}
