import { Component, OnInit } from '@angular/core';
import { WooCommerceService } from '../../services/woocommerce.service';
import { CommonModule } from '@angular/common';


@Component({
  selector: 'app-productos',
  imports: [CommonModule], // 📌 Agregar CommonModule aquí
  templateUrl: './productos.component.html',
  styleUrls: ['./productos.component.css']
})

/* Configura en services/woocommerce.service.ts con las claves API del Woocommer */
export class ProductosComponent implements OnInit {
  productos: any[] = [];

  constructor(private woocommerceService: WooCommerceService) {}

  ngOnInit(): void {
    this.cargarProductos();
  }

  cargarProductos() {
    this.woocommerceService.getProductos().subscribe((data: any) => {
      this.productos = data;
    });
  }

  agregarAlCarrito(productoId: number) {
    this.woocommerceService.addToCart(productoId);
  }

  comprarAhora(productoId: number) {
    window.location.href = `https://tu-subdominio.com/cart/?add-to-cart=${productoId}`;
  }

  darMeGusta(productoId: number) {
    this.woocommerceService.addToWishlist(productoId);
  }
}

