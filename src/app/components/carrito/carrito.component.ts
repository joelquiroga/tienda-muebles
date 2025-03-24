import { Component, OnInit } from '@angular/core';
import { loadStripe } from '@stripe/stripe-js';
import { CartService } from '../../services/cart.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-carrito',
  imports: [CommonModule],
  templateUrl: './carrito.component.html',
  styleUrls: ['./carrito.component.css']
})
export class CarritoComponent implements OnInit {
  cartItems: any[] = [];
  totalPrice: number = 0;
  cartOpen: boolean = false; // Estado del carrito
  stripePromise = loadStripe('XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX'); // Clave pública de Stripe

  constructor(private cartService: CartService) {}

  ngOnInit() {
    // Suscribirse a los cambios en el carrito para actualizar en tiempo real
    this.cartService.getCartItems().subscribe(items => {
      this.cartItems = items;
      this.calculateTotal(); // Recalcular total al cambiar el carrito
    });
  }

  toggleCart() {
    this.cartOpen = !this.cartOpen;
    const cartDropdown = document.getElementById('cartDropdown');
    const cartOverlay = document.getElementById('cartOverlay');
    if (this.cartOpen) {
      cartDropdown?.classList.add('active');
      cartOverlay?.classList.add('active');
    } else {
      cartDropdown?.classList.remove('active');
      cartOverlay?.classList.remove('active');
    }
  }

  removeItem(index: number) {
    this.cartService.removeFromCart(index);
  }

  calculateTotal() {
    this.totalPrice = this.cartItems.reduce((sum, item) => sum + (item.price * (item.quantity || 1)), 0);
  }

  async checkout() {
    if (this.cartItems.length === 0) {
      alert('Tu carrito está vacío');
      return;
    }

    try {
      // ✅ ACTUALIZACIÓN: usar el backend de tu VPS
      const response = await fetch('/api/stripe_checkout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ items: this.cartItems })
      });

      const session = await response.json();
      const stripe = await this.stripePromise;
      if (stripe) {
        stripe.redirectToCheckout({ sessionId: session.id });
      }
    } catch (error) {
      console.error('Error en el pago:', error);
      alert('Hubo un problema con el pago.');
    }
  }
}


