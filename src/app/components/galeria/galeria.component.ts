import { Component,  } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-galeria',
  imports: [CommonModule],
  templateUrl: './galeria.component.html',
  styleUrls: ['./galeria.component.css']
})

export class GaleriaComponent {
  titulo: string = 'Nuestra Galería Exclusiva';
  subtitulo: string = 'Descubre los mejores diseños para tu hogar';

  imagenSeleccionada: string | null = null;

  imagenes = [
    { url: 'https://i.pinimg.com/736x/a2/34/91/a234910dddd6b2b1b792c7e1f5c4105e.jpg', descripcion: 'Sofá moderno de cuero' },
    { url: 'https://media.adeo.com/media/2543697/media.jpg?width=3000&height=3000&format=jpg&quality=80&fit=bounds', descripcion: 'Mesa de comedor rústica' },
    { url: 'https://d.media.kavehome.com/image/upload/w_900,c_fill,ar_0.8,g_auto,f_auto/v1709219738/ambiences/A000001417_1.jpg', descripcion: 'Estante de madera elegante' },
    { url: 'https://d.media.kavehome.com/image/upload/w_900,c_fill,ar_0.8,g_auto,f_auto/v1737708505/ambiences/A25S003_071.jpg', descripcion: 'Silla minimalista' },
    { url: 'https://sususummer.com/cdn/shop/files/55a23667fd10c7a65cef5fde544ceba8.jpg?v=1740797900&width=990', descripcion: 'Lámpara de diseño exclusivo' },
    { url: 'https://i.pinimg.com/736x/a2/34/91/a234910dddd6b2b1b792c7e1f5c4105e.jpg', descripcion: 'Sofá moderno de cuero' },
    { url: 'https://media.adeo.com/media/2543697/media.jpg?width=3000&height=3000&format=jpg&quality=80&fit=bounds', descripcion: 'Mesa de comedor rústica' },
    { url: 'https://d.media.kavehome.com/image/upload/w_900,c_fill,ar_0.8,g_auto,f_auto/v1709219738/ambiences/A000001417_1.jpg', descripcion: 'Estante de madera elegante' },
    { url: 'https://d.media.kavehome.com/image/upload/w_900,c_fill,ar_0.8,g_auto,f_auto/v1737708505/ambiences/A25S003_071.jpg', descripcion: 'Silla minimalista' },
    { url: 'https://sususummer.com/cdn/shop/files/55a23667fd10c7a65cef5fde544ceba8.jpg?v=1740797900&width=990', descripcion: 'Lámpara de diseño exclusivo' },
    { url: 'https://i.pinimg.com/736x/a2/34/91/a234910dddd6b2b1b792c7e1f5c4105e.jpg', descripcion: 'Sofá moderno de cuero' },
    { url: 'https://media.adeo.com/media/2543697/media.jpg?width=3000&height=3000&format=jpg&quality=80&fit=bounds', descripcion: 'Mesa de comedor rústica' },
  ];

  abrirImagen(url: string) {
    this.imagenSeleccionada = url;
  }

  cerrarImagen() {
    this.imagenSeleccionada = null;
  }

  descargarPDF() {
    const link = document.createElement('a');
    link.href = 'assets/archivo.pdf'; // Ruta del PDF en tu proyecto
    link.download = 'archivo.pdf';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

}
