# EduPulse — Backend Laravel 11

Plateforme de gestion académique — API REST sécurisée par Bearer Token (Sanctum).

---

## Stack technique

| Technologie | Version |
|---|---|
| PHP | 8.2+ |
| Laravel | 11.x |
| Laravel Sanctum | 4.x |
| MySQL | 8.0+ |

---

## Installation

### 1. Cloner le dépôt
```bash
git clone https://github.com/votre-user/notes-backend-laravel.git
cd notes-backend-laravel
```

### 2. Installer les dépendances
```bash
composer install
```

### 3. Configurer l'environnement
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurer `.env`
```env
APP_NAME=EduPulse
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_notes_db
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost:3000
```

### 5. Créer la base de données et migrer
```bash
php artisan migrate
php artisan db:seed
```

### 6. Démarrer le serveur
```bash
php artisan serve
```

L'API est accessible sur `http://localhost:8000`

---

## Structure des dossiers

```
app/
├── Http/
│   ├── Controllers/Api/
│   │   ├── AuthController.php
│   │   ├── UserController.php
│   │   ├── ClasseController.php
│   │   ├── MatiereController.php
│   │   ├── NoteController.php
│   │   └── AffectationController.php
│   └── Middleware/
├── Models/
│   ├── User.php
│   ├── Classe.php
│   ├── Matiere.php
│   └── Note.php
└── Services/
database/
├── migrations/
└── seeders/
routes/
└── api.php
bootstrap/
└── app.php
```

---

## Endpoints API

### Authentification
| Méthode | Route | Description | Accès |
|---|---|---|---|
| POST | `/api/login` | Connexion | Public |
| POST | `/api/logout` | Déconnexion | Authentifié |
| GET | `/api/me` | Utilisateur connecté | Authentifié |

### Utilisateurs (Admin)
| Méthode | Route | Description |
|---|---|---|
| GET | `/api/users` | Liste tous les users |
| GET | `/api/users/stats` | Statistiques |
| POST | `/api/users` | Créer un user |
| PUT | `/api/users/{id}` | Modifier un user |
| DELETE | `/api/users/{id}` | Supprimer un user |

### Classes
| Méthode | Route | Description |
|---|---|---|
| GET | `/api/classes` | Liste (filtrée par rôle) |
| POST | `/api/classes` | Créer |
| PUT | `/api/classes/{id}` | Modifier |
| DELETE | `/api/classes/{id}` | Supprimer |
| GET | `/api/classes/{id}/etudiants` | Étudiants d'une classe |

### Matières
| Méthode | Route | Description |
|---|---|---|
| GET | `/api/matieres` | Liste (filtrée par rôle) |
| POST | `/api/matieres` | Créer |
| PUT | `/api/matieres/{id}` | Modifier |
| DELETE | `/api/matieres/{id}` | Supprimer |

### Notes
| Méthode | Route | Description |
|---|---|---|
| GET | `/api/notes` | Liste (filtrée par rôle) |
| POST | `/api/notes` | Saisie en masse |
| GET | `/api/notes/moyennes` | Calcul moyennes pondérées |
| GET | `/api/notes/{id}` | Détail d'une note |

### Affectations
| Méthode | Route | Description |
|---|---|---|
| GET | `/api/affectations` | Liste toutes les affectations |
| POST | `/api/affectations` | Créer une affectation |
| DELETE | `/api/affectations/{id}` | Supprimer |
| POST | `/api/affectations/etudiant` | Affecter étudiant à classe |

---

## Authentification Bearer Token

Toutes les routes protégées nécessitent le header :
```
Authorization: Bearer {token}
```

Le token est retourné lors du login :
```json
{
  "token": "1|xxxxxxxxxxxxxxxx",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Admin Système",
    "email": "admin@test.com",
    "role": "admin"
  }
}
```

---

## Comptes de démonstration

| Rôle | Email | Mot de passe |
|---|---|---|
| Admin | admin@test.com | password |
| Enseignant 1 | koffi@test.com | password |
| Enseignant 2 | marie@test.com | password |
| Étudiant 1 | etudiant1@test.com | password |
| Étudiant 2 | etudiant2@test.com | password |
| Étudiant 3 | etudiant3@test.com | password |
| Étudiant 4 | etudiant4@test.com | password |
| Étudiant 5 | etudiant5@test.com | password |

---

## Schéma de base de données

```
users: id, name, email, password, role, timestamps
classes: id, nom, annee_academique, timestamps
matieres: id, nom, code, coefficient_defaut, timestamps
notes: id, etudiant_id, matiere_id, enseignant_id, valeur, coefficient, type, date_evaluation, timestamps
enseignant_matiere_classe: id, enseignant_id, matiere_id, classe_id, annee, timestamps
etudiant_classe: id, etudiant_id, classe_id, annee, timestamps
```

---

## Déploiement production

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan migrate --force
```

Configurer `.env` avec les vraies valeurs DB et `APP_ENV=production`.