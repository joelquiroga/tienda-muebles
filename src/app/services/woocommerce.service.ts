/*
✅ Correcto. Si no has configurado la REST API de WooCommerce en tu WordPress, Angular no podrá obtener los productos.

Pasos clave para que funcione:

Habilitar la API de WooCommerce en tu WordPress:

Ve a WooCommerce > Configuración > Avanzado > REST API.
Crea una nueva clave API con permisos de "Lectura" o "Lectura/Escritura".
Guarda el Consumer Key y Consumer Secret.
Asegurar que WooCommerce permite peticiones desde tu dominio:

Si tu Angular está en otro dominio o subdominio, revisa los permisos CORS en tu servidor.

📌 Opción: Usar un plugin de WordPress para los los permisos CORS ----------------------------------
Si no puedes modificar .htaccess, puedes usar un plugin de CORS:

Instala "HTTP Headers" desde el repositorio de WordPress.
Ve a Ajustes > HTTP Headers y añade estas reglas:
Access-Control-Allow-Origin: https://tuangular.tu-dominio.com
Access-Control-Allow-Methods: GET, POST, OPTIONS
Access-Control-Allow-Headers: Content-Type, Authorization
Guarda los cambios.

Comprobar si CORS funciona ----------------------------------------------------------------------

Después de hacer los cambios, prueba si tu WooCommerce permite peticiones desde tu Angular.

Abre la consola del navegador (F12 > Consola) y escribe:
javascript
Copiar
Editar
fetch('https://tu-subdominio.com/wp-json/wc/v3/products')
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error(error));
Si ves los productos en consola, significa que CORS está bien configurado.
*/
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})

/*  Reemplaza TU_CONSUMER_KEY y TU_CONSUMER_SECRET por las credenciales que generaste en WordPress. */

export class WooCommerceService {
  private apiUrl = 'https://tu-subdominio.com/wp-json/wc/v3/products';
  private consumerKey = 'TU_CONSUMER_KEY';
  private consumerSecret = 'TU_CONSUMER_SECRET';

  constructor(private http: HttpClient) {}

  getProductos(): Observable<any> {
    return this.http.get(`${this.apiUrl}?consumer_key=${this.consumerKey}&consumer_secret=${this.consumerSecret}`);
  }

  addToCart(productoId: number) {
    window.location.href = `https://tu-subdominio.com/cart/?add-to-cart=${productoId}`;
  }

  addToWishlist(productoId: number) {
    // Implementar funcionalidad para wishlist si WooCommerce tiene soporte
    console.log(`Añadiendo producto ${productoId} a Me Gusta`);
  }
}