# 📁 Backend para Consultoría Web

## Estructura de archivos

```
consultoria-web/
├── config/
│   └── database.php          # Configuración de BD y correo
├── admin/
│   ├── login.php             # Login de administradores
│   ├── panel.php             # Panel para ver contactos
│   └── logout.php            # Cerrar sesión
├── sql/
│   └── consultoria_db.sql    # Script de la base de datos
├── procesar_contacto.php     # Procesa el formulario
├── contacto.html             # Página de contacto (ejemplo)
└── README.md                 # Este archivo
```

## 🚀 Instalación Rápida

### 1. Crear la Base de Datos

Abre **phpMyAdmin** o tu cliente MySQL y ejecuta el contenido de `sql/consultoria_db.sql`

O desde terminal:
```bash
mysql -u root -p < sql/consultoria_db.sql
```

### 2. Configurar conexión

Edita `config/database.php` y cambia:
- `DB_USER` - Tu usuario de MySQL (normalmente 'root')
- `DB_PASS` - Tu contraseña de MySQL
- `SMTP_USER` - Tu correo para enviar confirmaciones
- `SMTP_PASS` - Contraseña de aplicación de Google

### 3. Subir archivos

Copia todos los archivos PHP a tu servidor (XAMPP, WAMP, etc.)

### 4. Probar

- Formulario: `http://localhost/consultoria-web/contacto.html`
- Admin: `http://localhost/consultoria-web/admin/login.php`

## 🔐 Credenciales por defecto

| Usuario | Contraseña |
|---------|------------|
| admin   | admin123   |

## 📧 Configurar envío de correos (Gmail)

1. Ve a tu cuenta de Google > Seguridad
2. Activa verificación en 2 pasos
3. Genera una "Contraseña de aplicación"
4. Usa esa contraseña en `SMTP_PASS`

## 📱 El sitio es Responsivo

Todas las páginas se adaptan a:
- Desktop
- Tablet  
- Móvil

## ⚠️ Importante

- Cambia la contraseña del admin después de instalar
- En producción, usa HTTPS
- Configura correctamente el correo SMTP
