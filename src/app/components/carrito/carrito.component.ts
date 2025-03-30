import { Component, OnInit } from '@angular/core';
import { loadStripe } from '@stripe/stripe-js';
import { CartService } from '../../services/cart.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterLink, RouterLinkActive } from '@angular/router';

@Component({
  selector: 'app-carrito',
  standalone: true,
  imports: [CommonModule,FormsModule,RouterLink, RouterLinkActive],
  templateUrl: './carrito.component.html',
  styleUrls: ['./carrito.component.css']
})
export class CarritoComponent implements OnInit {
  cartItems: any[] = [];
  totalPrice: number = 0;
  cartOpen: boolean = false;
  stripePromise = loadStripe('pk_test_51PkQ4GRw4RJUah63uPLIMFD4yTWVTgNU6eQtX0vDpNRRhFdXslZLEpJFPakwHsFDfc15jasU6LIkqyu9TgO1PA2e00C5R8CS6a');

  //CUPON DESCUENTO ----------------------------
  couponCode: string = '';
  discount: number = 0;
  validCouponMessage: string = '';
  invalidCouponMessage: string = '';
  // Simulación de cupones válidos
  validCoupons: { [key: string]: number } = {
    'DESCUENTO10': 10,
    'MUEBLES20': 20
  };

  //-----------------------------------------------------------------------
  constructor(private cartService: CartService) {}

  ngOnInit() {
    this.cartService.getCartItems().subscribe(items => {
      this.cartItems = items;
      this.calculateTotal();
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

  increaseQuantity(index: number) {
    const item = this.cartItems[index];
    item.quantity = (item.quantity || 1) + 1;
    this.cartService.updateCart(this.cartItems);
    this.calculateTotal();
  }

  decreaseQuantity(index: number) {
    const item = this.cartItems[index];
    if ((item.quantity || 1) > 1) {
      item.quantity -= 1;
      this.cartService.updateCart(this.cartItems);
      this.calculateTotal();
    } else {
      this.removeItem(index);
    }
  }

  //CUPON DESCUENTO--------------------------

  applyCoupon() {
    const code = this.couponCode.trim().toUpperCase();
  
    if (this.validCoupons[code]) {
      this.discount = this.validCoupons[code];
      this.validCouponMessage = `Cupón aplicado: -${this.discount}%`;
      this.invalidCouponMessage = '';
  
      this.cartService.setDiscount(this.discount); // ✅ guardar globalmente
      this.calculateTotal();
    } else {
      this.discount = 0;
      this.validCouponMessage = '';
      this.invalidCouponMessage = 'Cupón no válido.';
      this.cartService.setDiscount(0); // ✅ quitar el descuento
      this.calculateTotal();
    }
  }
  
  calculateTotal() {
    const subtotal = this.cartItems.reduce((sum, item) => sum + (item.price * (item.quantity || 1)), 0);
    const discountAmount = subtotal * (this.discount / 100);
    this.totalPrice = subtotal - discountAmount;
  }

  // STRIPE VALIDACIÓN------------------------------------------------------------------

  async checkout() {
    if (this.cartItems.length === 0) {
      alert('Tu carrito está vacío');
      return;
    }

    try {
      const response = await fetch('/api/stripe_checkout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          items: this.cartItems,
          discount: this.discount // ✅ ENVÍA el descuento al backend
        })
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


