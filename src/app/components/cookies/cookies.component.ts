
import { Component, OnInit,  } from '@angular/core';
import { CommonModule } from '@angular/common';  // 👈 Importar aquí
import { RouterLink, RouterLinkActive } from '@angular/router';

@Component({
  selector: 'app-cookies',
  imports: [CommonModule,RouterLink, RouterLinkActive],
  templateUrl: './cookies.component.html',
  styleUrls: ['./cookies.component.css']
})
export class CookiesComponent implements OnInit {
  showCookies: boolean = false;

  ngOnInit() {
    const cookieAccepted = localStorage.getItem('cookiesAccepted');
    if (!cookieAccepted) {
      this.showCookies = true;
    }
  }

  acceptCookies() {
    localStorage.setItem('cookiesAccepted', 'true');
    this.showCookies = false;
  }
}
