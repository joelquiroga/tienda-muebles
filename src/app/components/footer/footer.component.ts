import { Component } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { CookiesComponent } from '../cookies/cookies.component';

@Component({
  selector: 'app-footer',
  imports: [RouterLink, RouterLinkActive,CookiesComponent],
  templateUrl: './footer.component.html',
  styleUrl: './footer.component.css'
})
export class FooterComponent {

}
