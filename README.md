# HiveMind

## Índex

- [Introducció](#introducció)
- [Requisits previs](#requisits-previs)
- [Guia d'instal·lació](#guia-dinstallacio)
- [Ús](#ús)
- [Autors](#autors)

## Introducció

HiveMind és una xarxa social moderna i dinàmica dissenyada per fomentar la interacció i la connexió entre usuaris. L'aplicació ofereix múltiples funcionalitats, permetent als usuaris crear comunitats, mantenir converses privades, compartir històries i publicar contingut multimèdia de manera senzilla i atractiva. Amb una interfície intuïtiva i una experiència d'usuari fluida, HiveMind té com a objectiu proporcionar un espai interactiu i enriquidor per a la comunicació digital.

## Requisits previs

- PHP 8.2 o superior  
- Composer  
- Node.js 18 o superior  
- npm o yarn  
- Base de dades MySQL o PostgreSQL  

## Guia d'instal·lacio

Per instal·lar el projecte, segueix aquests passos:

### Clonar el repositori

```sh
git clone https://github.com/2DAM-202425-PROJECTE/p2-g6-hivemind-app.git
cd p2-g6-hivemind-app
```

### Executar el script d'inici

L'script `start.sh` permet gestionar diverses opcions per configurar i executar el projecte. Pots executar-lo amb diferents paràmetres:

```sh
./start.sh [opcions]
```

### Opcions disponibles

| Opció            | Descripció                      |
|-----------------|--------------------------------|
| `--reset`       | Restableix la base de dades    |
| `--migrations`  | Executa les migracions        |
| `--seeders`     | Executa els seeders           |
| `--skip-workers` | No inicia els workers        |
| `--only-backend` | Inicia només el backend      |
| `--only-frontend` | Inicia només el frontend    |
| `--help`        | Mostra l'ajuda               |

### Exemple d'ús

Per iniciar el projecte amb la base de dades neta i executar les migracions:

```sh
./start.sh --reset
```

Per iniciar només el frontend:

```sh
./start.sh --only-frontend
```

## Ús

Després d'iniciar el projecte, podràs accedir a la plataforma des del teu navegador:

- **Frontend:** [http://localhost:3000](http://localhost:3000)  
- **Backend:** [http://localhost:8000/admin](http://localhost:8000/admin)  

Si s'han executat els seeders, es poden utilitzar els següents usuaris per accedir:

| Nom           | Correu electrònic | Contrasenya     |
|---------------|-------------------|-----------------|
| Admin	        | admin@example.com	| admin           |
| User	        | user@example.com	| user            |

## Autors

- **Radostin Valeriev Ivanov** - Backend Developer
- **Manel Méndez Alcántara** - Frontend Architect
- **Harry White** - UI/UX & Frontend Developer 
- **Gerard Moreno Campos** - Backend Architect
