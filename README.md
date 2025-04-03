# 🛒 Tienda Muebles – Angular + PHP + Stripe (VPS)

Este proyecto es una tienda online funcional con frontend en Angular y backend en PHP. Incluye autenticación, gestión de pedidos, integración con Stripe y un sistema robusto de logs, emails automáticos y seguridad, todo alojado en un servidor VPS.

---

## ✅ Funcionalidades

- Registro e inicio de sesión de usuarios (con protección CSRF)
- Gestión de carrito de compras en tiempo real (Angular)
- Proceso de compra con Stripe Checkout
- Inserción de órdenes y productos en MySQL
- Envío de correo automático de confirmación al cliente y copia oculta (BCC)
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
- **PHPMailer** – Envío de emails
- **Nginx** – Servidor web con seguridad CSP
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
│   ├── .env                           <-- STRIPE y MAIL config
│   └── vendor/                        <-- Composer (Stripe SDK, PHPMailer)
```

---

## 🔐 Seguridad

- `.env` oculto por configuración Nginx:  
  `location ~ /\. { deny all; }`
- Acceso al backend completamente restringido:  
  `location /backend { deny all; }`
- Rutas PHP específicas habilitadas con `location = /backend/archivo.php`
- Políticas de seguridad CSP aplicadas para prevenir inyecciones de estilos/scripts
- Comunicación segura Angular ↔ Backend mediante API
- Logging detallado activado en `/tmp/debug_env_path.log`

---

## 📧 Envío de correos

- Se usa **PHPMailer** para enviar confirmación de pedido al cliente
- Se envía una copia oculta (BCC) al administrador (`MAIL_BCC`)
- HTML mejorado con resumen visual del pedido e imagen de producto

### .env ejemplo para IONOS:
```env
STRIPE_SECRET_KEY=sk_test_...
MAIL_HOST=smtp.ionos.es
MAIL_PORT=587
MAIL_USERNAME=support@tudominio.com
MAIL_PASSWORD=TuContraseña
MAIL_FROM=support@tudominio.com
MAIL_FROM_NAME="Tienda Online QM"
MAIL_BCC=admin@tudominio.com
```

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

- Verifica en `/tmp/debug_env_path.log`
  - 📦 Datos del formulario
  - 👤 Usuario autenticado
  - ✅ Éxito de consultas SQL
  - ✅ Correo enviado
  - ✅ Stripe session creada

```bash
tail -f /tmp/debug_env_path.log
```

---

## 🔒 Seguridad Nginx y CSP

Configuración de seguridad en `default`:

- Evita carga de archivos ocultos (`.env`, `.git`)
- Solo permite rutas PHP específicas
- Protege el backend con reglas Nginx
- CSP para evitar scripts inyectados:

```nginx
add_header Content-Security-Policy "default-src 'self'; script-src 'self' https://js.stripe.com; style-src 'self' 'unsafe-inline'; img-src * data:;" always;
```

---

## 🚀 Cómo desplegar

```bash
# Subir archivos al VPS
scp -r ./proyecto usuario@IP:/var/www/angular-app

# Instalar dependencias
cd /var/www/backend
composer install

# Recargar Nginx
sudo systemctl reload nginx
```

---

## 📌 Próximas mejoras

- [x] Certificado SSL (Let's Encrypt) 🔒
- [x] Envío de email de confirmación
- [x] Copia oculta (BCC) al administrador
- [ ] Dominio personalizado
- [ ] Panel de administración para gestionar pedidos

---

## 👨‍💻 Autor

Desarrollado y migrado por el usuario, con soporte técnico paso a paso de IA 🤖🚀  
¡Un trabajo en equipo impecable para una solución sólida y profesional!