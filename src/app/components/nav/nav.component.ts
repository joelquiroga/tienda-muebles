import { Component, HostListener, OnInit, ElementRef, Renderer2 } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { Location } from '@angular/common';
import { Router } from '@angular/router';

@Component({
  selector: 'app-nav',
  imports: [ RouterLink, RouterLinkActive],
  templateUrl: './nav.component.html',
  styleUrls: ['./nav.component.css']
})
export class NavComponent implements OnInit {
  isMobile: boolean = false;
  menuOpen: boolean = false;
  submenuStates: { [key: string]: boolean } = {};
  iframeOverlay: HTMLElement | null = null; // Capa para manejar clics en iframe

  constructor(private location: Location, private router: Router, private elRef: ElementRef, private renderer: Renderer2) {}

  ngOnInit() {
    // Escuchar cambios en el historial del navegador para cerrar menús correctamente
    window.addEventListener('popstate', (event) => {
      this.handleBackNavigation();
    });

    this.isMobile = window.innerWidth <= 768; // Puedes ajustar este valor si quieres
  }

  toggleMenu() {
    this.menuOpen = !this.menuOpen;
    this.toggleIframeOverlay(this.menuOpen);
  }

  toggleSubmenu(submenuId: string) {
    if (this.isMobile) {
      // En móvil: togglear (abrir/cerrar)
      this.submenuStates[submenuId] = !this.submenuStates[submenuId];
    }
  }
  

  openSubmenu(menu: string) {
    this.submenuStates[menu] = true;
    history.pushState({}, '', location.href); // Agrega un estado al historial para manejar "atrás"
  }

  closeSubmenu(menu: string) {
    this.submenuStates[menu] = false;
  }

  handleBackNavigation() {
    const openSubmenus = Object.keys(this.submenuStates).filter(key => this.submenuStates[key]);
    
    if (openSubmenus.length > 0) {
      this.submenuStates[openSubmenus[openSubmenus.length - 1]] = false;
    } else if (this.menuOpen) {
      this.menuOpen = false;
      this.toggleIframeOverlay(false);
    } else {
      this.location.back();
    }
  }

  /**
   * Detecta clics fuera del menú y los submenús para cerrarlos automáticamente.
   */
  @HostListener('document:click', ['$event'])
  handleClickOutside(event: Event) {
    const menuElement = this.elRef.nativeElement.querySelector('.menu');
    const menuIcon = this.elRef.nativeElement.querySelector('.menu-icon');
    
    if (menuElement && !menuElement.contains(event.target as Node) && !menuIcon.contains(event.target as Node)) {
      this.menuOpen = false;
      Object.keys(this.submenuStates).forEach(menu => this.submenuStates[menu] = false);
      this.toggleIframeOverlay(false);
    }
  }

  /**
   * Agrega o quita una capa invisible sobre el iframe para capturar clics cuando el menú está abierto.
   */
  toggleIframeOverlay(show: boolean) {
    if (!this.iframeOverlay) {
        this.iframeOverlay = this.renderer.createElement('div');
        this.renderer.setStyle(this.iframeOverlay, 'position', 'absolute');
        this.renderer.setStyle(this.iframeOverlay, 'top', '0');
        this.renderer.setStyle(this.iframeOverlay, 'left', '0');
        this.renderer.setStyle(this.iframeOverlay, 'width', '100vw');
        this.renderer.setStyle(this.iframeOverlay, 'height', '100vh');
        this.renderer.setStyle(this.iframeOverlay, 'z-index', '999');
        this.renderer.setStyle(this.iframeOverlay, 'background', 'transparent');
        this.renderer.setStyle(this.iframeOverlay, 'display', 'none');

        this.renderer.listen(this.iframeOverlay, 'click', () => {
            this.menuOpen = false;
            this.toggleIframeOverlay(false);
        });

        // Asegurar que el iframeOverlay no es null antes de agregarlo al body
        if (this.iframeOverlay) {
            document.body.appendChild(this.iframeOverlay);
        }
    }

    // Asegurar que el iframeOverlay no es null antes de cambiar su display
    if (this.iframeOverlay) {
        this.iframeOverlay.style.display = show ? 'block' : 'none';
    }
}
}

