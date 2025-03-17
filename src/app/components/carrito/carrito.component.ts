import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';

@Component({
  selector: 'app-carrito',
  imports: [CommonModule],
  templateUrl: './carrito.component.html',
  styleUrl: './carrito.component.css'
})
export class CarritoComponent {
  cartItems = [
    { name: 'Producto 1', price: 100 },
    { name: 'Producto 2', price: 200 }
  ];

  toggleCart() {
    const cartDropdown = document.getElementById('cartDropdown');
    const cartOverlay = document.getElementById('cartOverlay');
    
    if (cartDropdown?.classList.contains('active')) {
        cartDropdown.classList.remove('active');
        cartOverlay?.classList.remove('active');
    } else {
        cartDropdown?.classList.add('active');
        cartOverlay?.classList.add('active');
    }
  }
}

