import { Component, OnInit } from '@angular/core';
import { CartService } from '../../services/cart.service';
import { CommonModule } from '@angular/common';
import { loadStripe } from '@stripe/stripe-js';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { FormsModule } from '@angular/forms';
import { RouterLink, RouterLinkActive } from '@angular/router';

@Component({
  selector: 'app-checkout',
  standalone: true,
  imports: [CommonModule, FormsModule, HttpClientModule, RouterLink, RouterLinkActive],
  templateUrl: './checkout.component.html',
  styleUrls: ['./checkout.component.css']
})
export class CheckoutComponent implements OnInit {
  cartItems: any[] = [];
  subtotal: number = 0;
  discount: number = 0;
  discountAmount: number = 0;
  total: number = 0;
  stripePromise = loadStripe('pk_test_51PkQ4GRw4RJUah63uPLIMFD4yTWVTgNU6eQtX0vDpNRRhFdXslZLEpJFPakwHsFDfc15jasU6LIkqyu9TgO1PA2e00C5R8CS6a'); // TEST de prueba

  // Usuario
  usuario: any = null;

  // Formulario de facturación
  facturacion = {
    nombre: '',
    telefono: '',
    direccion: '',
    ciudad: '',
    provincia: '',
    codigo_postal: '',
    comentarios: ''
  };

  constructor(private cartService: CartService, private http: HttpClient) {}

  ngOnInit() {
    this.cartService.getCartItems().subscribe(items => {
      this.cartItems = items;
      this.calculateTotals();
    });

    this.cartService.getDiscount().subscribe(discount => {
      this.discount = discount;
      this.calculateTotals();
    });

    // Cargar usuario logueado
    this.http.get<any>('/api_get_usuario.php', { withCredentials: true }).subscribe({
      next: (response) => {
        if (response.status === 'success') {
          this.usuario = response.usuario;
        }
      },
      error: (error) => {
        console.error('❌ Error al obtener usuario:', error);
      }
    });
  }

  calculateTotals() {
    this.subtotal = this.cartItems.reduce((sum, item) => sum + item.price * item.quantity, 0);
    this.discountAmount = this.subtotal * (this.discount / 100);
    this.total = this.subtotal - this.discountAmount;
  }

  finalizarCompra() {
    if (!this.usuario) {
      alert('Debes iniciar sesión antes de finalizar la compra.');
      return;
    }

    const f = this.facturacion;
    if (!f.nombre || !f.telefono || !f.direccion || !f.ciudad || !f.provincia || !f.codigo_postal) {
      alert('Por favor completa todos los campos obligatorios.');
      return;
    }

    const datos = {
      facturacion: this.facturacion,
      items: this.cartItems,
      discount: this.discount
    };

    this.http.post<any>('/api_procesar_orden.php', datos, { withCredentials: true }).subscribe({
      next: async (response) => {
        if (response.id) {
          const stripe = await this.stripePromise;
          if (stripe) {
            const { error } = await stripe.redirectToCheckout({ sessionId: response.id });
            if (error) {
              console.error('⚠️ Error de Stripe:', error.message);
              alert('Error al redirigir a Stripe: ' + error.message);
            }
          }
        } else {
          alert('Error al crear la sesión de pago.');
          console.error('Respuesta de error:', response);
        }
      },
      error: (err) => {
        alert('Hubo un error al procesar la orden.');
        console.error('❌ Error HTTP:', err);
        if (err.error) {
          console.error('🧾 Detalle del error:', err.error);
        }
      }
    });
  }
}
