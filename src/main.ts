
// Importa la función bootstrapApplication para iniciar la aplicación sin usar NgModule.
import { bootstrapApplication } from '@angular/platform-browser';
//Importa el componente principal de la aplicación (AppComponent), que será la raíz de Angular.
import { AppComponent } from './app/app.component';
// Importa provideRouter() y las rutas definidas en app.routes.ts.
import { provideRouter } from '@angular/router';
import { routes } from './app/app.routes';
// Importa appConfig, que contiene los providers configurados en app.config.ts.
import { appConfig } from './app/app.config';

import { provideHttpClient } from '@angular/common/http';

/*
✅bootstrapApplication(AppComponent, {...}) inicia la aplicación de Angular con la configuración adecuada.
✅ providers: [...appConfig.providers, provideRouter(routes)] fusiona los proveedores definidos en appConfig con los del enrutador.
✅ .catch(err => console.error(err)); captura y muestra errores si algo falla en el arranque de la aplicación.
*/

bootstrapApplication(AppComponent, {
  providers: [...appConfig.providers, provideRouter(routes),provideHttpClient()]
}).catch(err => console.error(err));

/*
Con este cambio, no perderás la configuración de routes ni de appConfig.
Ahora, todo se ejecuta correctamente en una única llamada a bootstrapApplication.🚀
*/
