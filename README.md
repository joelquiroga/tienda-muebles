
# 🛒 Tienda Muebles – Angular + PHP + Stripe (VPS)

Este proyecto es una tienda online funcional con frontend en Angular y backend en PHP. Cuenta con autenticación de usuarios, gestión de carrito de compras y pasarela de pago Stripe, todo migrado exitosamente desde un hosting compartido a un VPS propio.

---

## ✅ Funcionalidades

- Registro e inicio de sesión de usuarios (con protección CSRF)
- Carrito dinámico con Angular + Stripe
- Pagos reales (modo test ahora, listo para modo LIVE)
- Panel de gestión de base de datos vía phpMyAdmin
- Uso de variables de entorno `.env` seguras para claves privadas
- Backend y API aisladas, seguras y protegidas

---

## 🛠 Tecnologías

- **Angular 17** – Frontend SPA
- **PHP 8.3** – Backend
- **MySQL** – Base de datos
- **Stripe** – Procesamiento de pagos
- **Nginx** – Servidor web
- **Composer** – Dependencias PHP
- **Dotenv** – Manejo de variables de entorno

---

## 📁 Estructura de Archivos

```
/var/www/
├── angular-app/
│   ├── index.html, js, css
│   ├── index.php
│   ├── api/
│   │   ├── stripe_checkout.php
│   │   ├── .env               <-- Clave privada Stripe (test/live)
│   │   └── vendor/
│   ├── api_login.php
│   ├── api_registro.php
│   ├── success.html
│   └── cancel.html
├── backend/
│   ├── registro_usuario.php
│   ├── login_usuario.php
│   └── conexion.php
```

---

## 🔐 Seguridad

- `.env` está fuera del frontend, protegido con Nginx (`location ~ /\.`)
- `/backend` completamente denegado desde la web (`deny all`)
- Stripe solo accesible vía `/api/stripe_checkout.php`
- phpMyAdmin configurado de forma segura
- Variables CSRF en formularios sensibles

---

## 🚀 Cómo desplegar

```bash
# Clonar proyecto
scp -r ./proyecto_web usuario@IP:/home/usuario

# Actualizar frontend y backend en Nginx
bash deploy.sh

# Reiniciar servidor
sudo systemctl restart nginx
```

---

## 🧪 Stripe de prueba

- Archivo `.env` debe tener:
```
STRIPE_SECRET_KEY=sk_test_xxxxxxxxxxxxxxxxx
```

- Para test, usar tarjeta: `4242 4242 4242 4242`  
  Cualquier fecha futura, CVC, y ZIP

---

## 📌 Pendiente / Producción

- [ ] Instalar certificado HTTPS (Let's Encrypt)
- [ ] Añadir dominio personalizado (ej. IONOS)
- [ ] Pasar a clave LIVE de Stripe
- [ ] Agregar roles o panel de administración (opcional)

---

## 📄 Autor
Migración y desarrollo por el usuario con asistencia técnica de IA 🧠⚡
