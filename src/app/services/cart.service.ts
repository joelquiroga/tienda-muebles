import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class CartService {
  private cartItems: BehaviorSubject<any[]> = new BehaviorSubject<any[]>([]);
  private discount: BehaviorSubject<number> = new BehaviorSubject<number>(0); // cupón

  constructor() {}

  // Carrito
  getCartItems() {
    return this.cartItems.asObservable();
  }

  addToCart(product: any) {
    const items = this.cartItems.getValue();
    const existingItem = items.find(item => item.name === product.name);

    if (existingItem) {
      existingItem.quantity = (existingItem.quantity || 1) + 1;
    } else {
      items.push({ ...product, quantity: 1 });
    }

    this.cartItems.next([...items]);
  }

  removeFromCart(index: number) {
    const items = this.cartItems.getValue();
    items.splice(index, 1);
    this.cartItems.next([...items]);
  }

  clearCart() {
    this.cartItems.next([]);
  }

  updateCart(items: any[]) {
    this.cartItems.next([...items]);
  }

  // Cupón de descuento
  setDiscount(percent: number) {
    this.discount.next(percent);
  }

  getDiscount() {
    return this.discount.asObservable();
  }
  
}

