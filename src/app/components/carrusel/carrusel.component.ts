import { Component } from '@angular/core';
import { ElementRef, ViewChild, AfterViewInit, OnDestroy } from '@angular/core';

@Component({
  selector: 'app-carrusel',
  imports: [],
  templateUrl: './carrusel.component.html',
  styleUrl: './carrusel.component.css'
})


export class CarruselComponent implements AfterViewInit, OnDestroy {
  @ViewChild('carousel', { static: false }) carousel!: ElementRef;

  currentIndex = 0;
  totalItems = 0;
  autoScroll!: any;

  ngAfterViewInit() {
    setTimeout(() => {
      if (this.carousel?.nativeElement?.children.length > 0) {
        this.totalItems = this.carousel.nativeElement.children.length;
        this.updateCarousel();
        this.startAutoScroll();
      } else {
        console.warn('El carrusel no tiene elementos aún');
      }
    }, 300); // Aumentamos el tiempo de espera para asegurar la carga
  }  


  updateCarousel() {
    if (this.carousel) {
      const offset = -this.currentIndex * 100;
      this.carousel.nativeElement.style.transform = `translateX(${offset}%)`;
    }
  }

  nextSlide() {
    this.currentIndex = (this.currentIndex + 1) % this.totalItems;
    this.updateCarousel();
  }

  prevSlide() {
    this.currentIndex = (this.currentIndex - 1 + this.totalItems) % this.totalItems;
    this.updateCarousel();
  }

  startAutoScroll() {
    this.autoScroll = setInterval(() => this.nextSlide(), 10000);
  }

  stopAutoScroll() {
    clearInterval(this.autoScroll);
  }

  ngOnDestroy() {
    this.stopAutoScroll();
  }
}
