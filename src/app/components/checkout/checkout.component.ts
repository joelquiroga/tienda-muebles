import { Component, NgModule } from '@angular/core';
import { loadStripe } from '@stripe/stripe-js';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-checkout',
  imports: [],
  templateUrl: './checkout.component.html',
  styleUrl: './checkout.component.css'
})

export class CheckoutComponent {
  constructor(private http: HttpClient) {}

  async checkout() {
    const stripe = await loadStripe('pk_test_51PkQ4GRw4RJUah63uPLIMFD4yTWVTgNU6eQtX0vDpNRRhFdXslZLEpJFPakwHsFDfc15jasU6LIkqyu9TgO1PA2e00C5R8CS6a'); // clave pública real

    if (!stripe) {
      console.error('Stripe no se pudo cargar correctamente');
      return;
    }

    this.http.post('/.netlify/functions/create-checkout', {})
      .subscribe(async (res: any) => {
        const result = await stripe.redirectToCheckout({
          sessionId: res.id,
        });
        if (result.error) {
          console.error(result.error.message);
        }
      });
  }
  
}
