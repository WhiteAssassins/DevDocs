# DevDocs

Archivo web de documentaciones para desarrolladores, mantenido por **WhiteAssassins**.

Sitio: https://aewhitedevs.com

## Estado

Este es un proyecto antiguo basado en CodeIgniter 3. Se mantiene como proyecto público/legacy, con limpieza básica para correr en entornos modernos sin exponer credenciales ni datos reales.

## Requisitos

- PHP 8.0+ recomendado
- Apache con `mod_rewrite`
- MariaDB o MySQL
- CodeIgniter 3.1.13 incluido en `system/`

## Instalacion local

1. Clona o copia el proyecto dentro de tu servidor web, por ejemplo `C:\xampp\htdocs\DevDocs`.
2. Crea la base de datos:

```sql
CREATE DATABASE devdocs CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

3. Importa el esquema:

```bash
mysql -u root -p devdocs < database/schema.sql
```

4. Opcionalmente importa datos de ejemplo:

```bash
mysql -u root -p devdocs < database/seed.example.sql
```

5. Configura variables de entorno en Apache, sistema operativo o panel de hosting:

```bash
APP_BASE_URL=http://127.0.0.1/devdocs/
APP_ENCRYPTION_KEY=change-this-to-a-long-random-string
DB_HOST=localhost
DB_NAME=devdocs
DB_USER=root
DB_PASS=
```

Si no defines variables, la app usa valores locales seguros para desarrollo.

## Administracion

La base publica no incluye usuarios reales. Crea tu usuario desde el modal de registro de la app y luego entra por Login.

Las contraseñas nuevas se guardan con `password_hash()`. Los hashes MD5 antiguos se aceptan solo para migracion: al iniciar sesion correctamente, se actualizan automaticamente.

## Base de datos publica

- `database/schema.sql`: esquema limpio para GitHub.
- `database/seed.example.sql`: datos de ejemplo sin credenciales reales.
- `devdocs.sql`: snapshot compatible, tambien sin usuarios reales.

## Notas legacy

El codigo conserva partes antiguas del proyecto original, incluyendo integraciones que pueden requerir trabajo adicional si se quieren reactivar. La limpieza actual prioriza que el proyecto sea publicable, instalable y entendible sin hacer una reescritura completa.

## Copyright

Copyright 2026 WhiteAssassins.

https://aewhitedevs.com
