
# Despliegue de una Aplicación Symfony y Angular con Docker Compose
Este proyecto utiliza Docker y Docker Compose para desplegar una aplicación que incluye un backend Symfony, un frontend Angular y una base de datos PostgreSQL de manera rápida y sencilla.

---

## Requisitos Previos
Antes de comenzar, asegúrate de tener instalados en tu sistema:

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)
---

## Instalación y Puesta en Marcha

### 1. Clonar el repositorio
Ejecuta el siguiente comando para clonar el proyecto:
```bash
git clone git@github.com:campus-CodeArts/Onboarding-SymfAngular.git
cd Onboarding-SymfAngular
```

### 2. Levantar los contenedores
Para iniciar los servicios en segundo plano, ejecuta:
```bash
docker-compose up -d
```
📌 **Nota:** La primera vez que inicies los servicios, puede tardar unos minutos en configurarse completamente.

### 3. Verificar que los contenedores están corriendo
Comprueba el estado de los contenedores con:
```bash
docker ps
```
Deberías ver tres contenedores en ejecución: **PostgreSQL**, **Symfony (backend)** y **Angular (frontend)**.

### 4. Acceder a la aplicación
- **Frontend:** Abre la siguiente URL en tu navegador:
  ```
  http://localhost:4200
  ```
- **Backend (Symfony):** Puedes ver la salida de Symfony desde:
  ```
  http://localhost:8000
  ```
- **Base de datos PostgreSQL:** El contenedor de la base de datos está en el puerto 5432, aunque normalmente no es necesario acceder directamente a este servicio en un navegador.

---

## Detener y Reiniciar los Contenedores
Si deseas detener los contenedores en ejecución:
```bash
docker compose down
```
Para volver a iniciarlos:
```bash
docker compose up -d
```

---

## Eliminar los Contenedores y Datos Persistentes
Si quieres eliminar los contenedores junto con los volúmenes y datos almacenados:
```bash
docker compose down -v
```
⚠️ **Advertencia:** Esto eliminará todos los datos almacenados en la base de datos PostgreSQL. ⚠️ 

---

## API 

### 🛡️ Admin — `/api/admin`

| Método | Endpoint | Descripción | Rol |
|--------|----------|-------------|------|
| GET | `/api/admin/users` | Listar todos los usuarios | Admin |
| GET | `/api/admin/users/{id}` | Ver detalle de un usuario | Admin |
| PUT | `/api/admin/users/{id}` | Actualizar roles de un usuario | Admin |
| DELETE | `/api/admin/users/{id}` | Eliminar usuario | Admin |

### 🧑‍🏫 Coach — `/api/coach`

| Método | Endpoint | Descripción | Rol |
|--------|----------|-------------|------|
| GET | `/api/coach/info` | Información del entrenador | Coach |
| GET | `/api/coach/players` | Jugadores del equipo del coach | Coach |

### 📩 Invitaciones — `/api/invitations`

| Método | Endpoint | Descripción | Rol |
|--------|----------|-------------|------|
| POST | `/api/invitations` | Crear invitación para un jugador | Coach |
| GET | `/api/invitations/player` | Listar invitaciones del jugador | Player |
| PATCH | `/api/invitations/{id}/respond` | Aceptar o rechazar invitación | Player |

### 🏆 Teams — `/api/team`

| Método | Endpoint | Descripción | Rol |
|--------|----------|-------------|------|
| POST | `/api/team` | Crear un equipo | Coach |
| GET | `/api/team` | Obtener info del equipo del coach | Coach |
| POST | `/api/team/{id}/add-player` | Añadir jugador al equipo | Coach |
| GET | `/api/team/available` | Listar equipos disponibles | Público |
| POST/PUT | `/api/team/{id}` | Actualizar información del equipo | Coach |
| DELETE | `/api/team/{id}` | Eliminar equipo | Coach |
| PATCH | `/api/team/{id}/remove-player` | Eliminar jugador del equipo | Coach |
| GET | `/api/team/{id}` | Ver información del equipo (admin o dueño) | Admin/Coach |

### 🧍‍♂️ Player — `/api/player`

| Método | Endpoint | Descripción | Rol |
|--------|----------|-------------|------|
| GET | `/api/player/trainings/{id}` | Ver un entrenamiento | Player |
| GET | `/api/player/matches/{id}` | Ver un partido | Player |
| GET | `/api/player/available` | Listar jugadores disponibles | Coach |
| GET | `/api/player/team` | Obtener equipo del jugador | Player |

### 🙋 Me — `/api/me`

| Método | Endpoint | Descripción | Rol |
|--------|----------|-------------|------|
| GET | `/api/me` | Información del usuario autenticado | Cualquier usuario logueado |

### 🗓️ Eventos — `/api/events`

| Método | Endpoint | Descripción | Rol |
|--------|----------|-------------|------|
| POST | `/api/events` | Crear evento (entrenamiento o partido) | Coach |
| POST | `/api/events/{id}/images` | Subir imágenes al evento | Coach |
| GET | `/api/events/team/{teamId}` | Listar eventos por equipo | Coach/Player |
| PUT | `/api/events/{id}` | Editar evento | Coach |

### 🧾 Auth — Registro

| Método | Endpoint | Descripción | Rol |
|--------|----------|-------------|------|
| POST | `/api/register` | Registrar un usuario (coach o player) | Público |
