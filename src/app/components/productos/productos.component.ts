import { Component } from '@angular/core';
import { CartService } from '../../services/cart.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-productos',
  imports: [CommonModule],
  templateUrl: './productos.component.html',
  styleUrls: ['./productos.component.css']
})
export class ProductosComponent {
  products = [
    { 
      name: 'Mesa de Madera', 
      price: 5000, 
      images: ['https://i.pinimg.com/736x/bb/b7/50/bbb75028301e7762b38abb56c9762632.jpg',
         'https://i.pinimg.com/736x/bb/b7/50/bbb75028301e7762b38abb56c9762632.jpg',
          'https://i.pinimg.com/736x/bb/b7/50/bbb75028301e7762b38abb56c9762632.jpg']
    },
    { 
      name: 'Silla de Metal', 
      price: 2500, 
      images: ['https://i.pinimg.com/736x/68/11/4a/68114ab6fc1c204487e46ee7fd60c9f0.jpg',
         'https://i.pinimg.com/736x/68/11/4a/68114ab6fc1c204487e46ee7fd60c9f0.jpg',
          'https://i.pinimg.com/736x/68/11/4a/68114ab6fc1c204487e46ee7fd60c9f0.jpg']
    },
    { 
      name: 'Lámpara Moderna', 
      price: 1500, 
      images: ['https://i.pinimg.com/736x/d7/60/9e/d7609e7d2d0088c471b72afd879a364f.jpg',
         'https://i.pinimg.com/736x/d7/60/9e/d7609e7d2d0088c471b72afd879a364f.jpg',
          'https://i.pinimg.com/736x/d7/60/9e/d7609e7d2d0088c471b72afd879a364f.jpg']
    }
  ];

  selectedProduct: any = null;
  selectedImageIndex: number = 0;

  constructor(private cartService: CartService) {}

  toastMessage = '';

  addToCart(product: any) {
    this.cartService.addToCart(product);
    this.showToast(`${product.name} agregado al carrito`);
  }
  
  showToast(message: string) {
    this.toastMessage = message;
    setTimeout(() => {
      this.toastMessage = '';
    }, 3000); // mensaje desaparece a los 3 segundos
  }

  openModal(product: any) {
    this.selectedProduct = product;
    this.selectedImageIndex = 0;
    document.body.classList.add('no-scroll'); // Bloquear scroll
  }

  closeModal() {
    this.selectedProduct = null;
    document.body.classList.remove('no-scroll'); // Restaurar scroll
  }

  changeImage(index: number) {
    this.selectedImageIndex = index;
  }
}
