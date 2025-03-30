# 🛒 Tienda Muebles – Angular + PHP + Stripe (VPS)

Este proyecto es una tienda online funcional con frontend en Angular y backend en PHP. Incluye autenticación, gestión de pedidos, integración con Stripe y un sistema robusto de logs y seguridad, todo alojado en un servidor VPS.

---

## ✅ Funcionalidades

- Registro e inicio de sesión de usuarios (con protección CSRF)
- Gestión de carrito de compras en tiempo real (Angular)
- Proceso de compra con Stripe Checkout
- Inserción de órdenes y productos en MySQL
- Manejo de sesiones con PHP y cookies seguras
- Uso de logs (`/tmp/debug_env_path.log`) para depurar errores
- API intermedia (`api_procesar_orden.php`) para acceder de forma segura al backend
- Carga de claves privadas desde `.env` en el backend
- Redirección automática a Stripe en el proceso de compra

---

## 🛠 Tecnologías Utilizadas

- **Angular 17** – Frontend SPA
- **PHP 8.3** – Backend
- **MySQL** – Base de datos
- **Stripe** – Pasarela de pago
- **Dotenv** – Variables de entorno seguras
- **Nginx** – Servidor web
- **phpMyAdmin** – Gestión de base de datos
- **Composer** – Dependencias PHP

---

## 📁 Estructura del Proyecto

```
/var/www/
├── angular-app/
│   ├── api_procesar_orden.php         <-- Llama internamente a backend/procesar_orden.php
│   ├── api_get_usuario.php
│   ├── api_login.php / api_registro.php
│   ├── index.html (Angular)
│   ├── cancel.html / success.html
│   └── api/stripe_checkout.php
├── backend/
│   ├── procesar_orden.php             <-- Código principal del pedido
│   ├── conexion.php                   <-- Conexión DB protegida
│   ├── get_usuario.php / registro_usuario.php
│   ├── .env                           <-- STRIPE_SECRET_KEY
│   └── vendor/                        <-- Composer (Stripe SDK)
```

---

## 🔐 Seguridad

- `.env` oculto por configuración Nginx: `location ~ /\. { deny all; }`
- Acceso al backend completamente restringido (`location /backend { deny all; }`)
- Rutas PHP específicas liberadas con `location = /backend/archivo.php`
- Comunicación segura Angular ↔ Backend mediante API intermedia
- Logging detallado activado en `/tmp/debug_env_path.log`

---

## 🧪 Modo de Prueba con Stripe

- El archivo `.env` debe contener:
```env
STRIPE_SECRET_KEY=sk_test_xxxxxxxxxxxxxxxxx
```

- Usa esta tarjeta de prueba para pagos:
```
4242 4242 4242 4242
Fecha: cualquier fecha futura
CVC: cualquier número
ZIP: cualquier código
```

---

## 🧰 Diagnóstico y Debug

- El backend escribe información de depuración en: `/tmp/debug_env_path.log`
- Revisa:
  - Cookies y sesión (`PHPSESSID`)
  - Datos recibidos del formulario
  - Logs antes y después de ejecutar consultas SQL
  - Respuestas de Stripe

---

## 🚀 Cómo desplegar

```bash
# Subir archivos al VPS
scp -r ./proyecto usuario@IP:/var/www/angular-app

# Reiniciar Nginx
sudo systemctl reload nginx

# Ver logs en tiempo real
tail -f /tmp/debug_env_path.log
```

---

## 📌 Próximas mejoras

- [ ] Certificado SSL (Let's Encrypt)
- [ ] Dominio personalizado (ej. mueblestore.com)
- [ ] Activar modo LIVE de Stripe
- [ ] Envío de email de confirmación
- [ ] Panel de administración para gestionar pedidos

---

## 👨‍💻 Autor

Desarrollado y migrado por el usuario, con soporte técnico paso a paso de IA 🤖🚀  
¡Un trabajo en equipo impecable para una solución sólida y profesional!
