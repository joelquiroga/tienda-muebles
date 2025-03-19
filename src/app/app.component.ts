import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { NavComponent } from "./components/nav/nav.component";
import { FooterComponent } from "./components/footer/footer.component";
import { CarritoComponent } from './components/carrito/carrito.component';



@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, NavComponent, FooterComponent,CarritoComponent,],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent {
  title = 'tienda-muebles';
}
