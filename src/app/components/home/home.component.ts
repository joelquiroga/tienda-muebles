import { ProductosComponent } from "../productos/productos.component";
import { Component, ElementRef, ViewChild, AfterViewInit, OnDestroy } from '@angular/core';
import { CarruselComponent } from "../carrusel/carrusel.component";

@Component({
  selector: 'app-home',
  imports: [ProductosComponent, CarruselComponent],
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})

export class HomeComponent {

}
