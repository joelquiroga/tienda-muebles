import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class CartService {
  private cartItems: BehaviorSubject<any[]> = new BehaviorSubject<any[]>([]);

  constructor() {}

  // Obtener productos del carrito como un Observable
  getCartItems() {
    return this.cartItems.asObservable();
  }

  // Agregar producto al carrito y notificar cambios
  addToCart(product: any) {
    const items = this.cartItems.getValue();
    items.push(product);
    this.cartItems.next([...items]); // Emitir cambios
  }

  // Eliminar producto del carrito por índice y notificar cambios
  removeFromCart(index: number) {
    const items = this.cartItems.getValue();
    items.splice(index, 1);
    this.cartItems.next([...items]); // Emitir cambios
  }

  // Vaciar carrito después del pago y notificar cambios
  clearCart() {
    this.cartItems.next([]); // Emitir carrito vacío
  }
}

