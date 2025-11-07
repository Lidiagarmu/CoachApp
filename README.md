
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

## Estructura de la aplicación

APP
│
├── HomeComponent
├── LoginComponent
├── RegisterComponent
│   ├── CoachRegisterComponent
│   └── PlayerRegisterComponent
│
└── DASHBOARDS
     
     1️⃣ COACH DASHBOARD (ruta: /coach-dashboard)
     ├─ Sidebar (visible fijo)
     │   ├─ Equipo -> CoachTeamComponent
     │   │     ├─ Crear Equipo (formulario)
     │   │     └─ Info Equipo + Plantilla jugadores (TeamService)
     │   ├─ Jugadores -> CoachPlayersComponent
     │   │     ├─ Lista jugadores sin equipo (PlayerService)
     │   │     └─ Botón Invitar (TeamInvitationService)
     │   ├─ Eventos -> CoachEventsComponent
     │   │     ├─ Trainings -> CoachTrainingsComponent (EventService)
     │   │     └─ Matches -> CoachMatchesComponent (EventService)
     │   └─ Ajustes -> CoachSettingsComponent
     │           ├─ Editar info coach
     │           ├─ Editar info equipo (escudo, nombre)
     │           └─ Eliminar equipo / eliminar jugador
     └─ Contenido derecho del dashboard
           └─ Carga dinámica según botón del sidebar

     -------------------------------
     2️⃣ PLAYER DASHBOARD (ruta: /player-dashboard)
     ├─ Sidebar (visible fijo)
     │   ├─ Equipo -> PlayerTeamComponent
     │   │     ├─ Invitaciones pendientes (TeamInvitationService)
     │   │     ├─ Aceptar/Rechazar invitaciones
     │   │     └─ Info equipo o mensaje "No tienes equipo"
     │   ├─ Eventos -> PlayerEventsComponent
     │   │     ├─ Trainings -> PlayerTrainingsComponent (EventService)
     │   │     └─ Matches -> PlayerMatchesComponent (EventService)
     │   └─ Ajustes -> PlayerSettingsComponent
     │           ├─ Editar info usuario (nombre, foto, dorsal)
     │           └─ Abandonar equipo (TeamService)
     └─ Contenido derecho del dashboard
           └─ Carga dinámica según botón del sidebar

     -------------------------------
     3️⃣ ADMIN DASHBOARD (ruta: /admin-dashboard)
     ├─ Sidebar (visible fijo)
     │   ├─ Usuarios -> AdminUsersComponent (UserService)
     │   │     ├─ Coaches
     │   │     ├─ Players
     │   │     └─ Eliminar usuarios
     │   └─ Equipos -> AdminTeamsComponent (TeamService)
     │           └─ Lista de equipos
     └─ Contenido derecho del dashboard
           └─ Carga dinámica según botón del sidebar

