# API Matchmaking - Documentation

## Authentification

L'API utilise JWT (JSON Web Tokens) pour l'authentification. Après inscription ou connexion, vous recevez un token à inclure dans le header `Authorization: Bearer {token}` pour toutes les requêtes protégées.

### Endpoints Publics

#### POST /api/register
Inscription d'un nouvel utilisateur.

**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "username": "username"
}
```

**Réponse:**
```json
{
  "token": "jwt_token_here",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "username": "username"
  }
}
```

#### POST /api/login
Connexion d'un utilisateur.

**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Réponse:**
```json
{
  "token": "jwt_token_here",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "username": "username"
  }
}
```

## Endpoints Utilisateur / Profil

### GET /api/me
Récupère le profil de l'utilisateur connecté.

**Headers:** `Authorization: Bearer {token}`

### PUT /api/me
Met à jour le profil de l'utilisateur connecté.

**Body:**
```json
{
  "username": "new_username",
  "bio": "Ma bio",
  "gender": "male",
  "age": 25,
  "location": "Paris",
  "latitude": "48.8566",
  "longitude": "2.3522",
  "preferences": {
    "age_range": [18, 35],
    "distance_km": 50,
    "gender_pref": "female"
  },
  "tags": ["sport", "musique"]
}
```

### POST /api/me/avatar
Upload d'un avatar (multipart/form-data).

**Form Data:**
- `avatar`: fichier image

### GET /api/users
Liste des utilisateurs avec pagination.

**Query params:**
- `page`: numéro de page (défaut: 1)
- `limit`: nombre d'éléments par page (défaut: 20, max: 50)

### GET /api/users/{id}
Détails d'un utilisateur.

### GET /api/recommendations
Recommandations d'utilisateurs basées sur les préférences.

**Query params:**
- `limit`: nombre de recommandations (défaut: 20)

## Matchmaking / Interactions

### POST /api/users/{id}/like
Like un utilisateur.

### POST /api/users/{id}/dislike
Dislike un utilisateur.

### POST /api/users/{id}/superlike
Superlike un utilisateur (donne +10 XP).

### POST /api/users/{id}/wink
Envoie un clin d'œil à un utilisateur.

### GET /api/matches
Liste des matches de l'utilisateur connecté.

### GET /api/matches/{id}
Détails d'un match.

### GET /api/matches/{id}/messages
Messages d'un match.

**Query params:**
- `limit`: nombre de messages (défaut: 50)
- `offset`: offset pour pagination

### POST /api/matches/{id}/message
Envoyer un message dans un match.

**Body:**
```json
{
  "content": "Salut ! Comment ça va ?"
}
```

## Gamification

### GET /api/me/gamification
Récupère les informations de gamification (XP, level, badges, quêtes).

**Réponse:**
```json
{
  "level": 5,
  "xp": 450,
  "score": 650,
  "badges": [...],
  "quests": [...]
}
```

### GET /api/leaderboard
Classement des utilisateurs.

**Query params:**
- `limit`: nombre d'utilisateurs (défaut: 10, max: 100)

## Notifications

### GET /api/notifications
Liste des notifications.

**Query params:**
- `limit`: nombre de notifications (défaut: 50)
- `offset`: offset pour pagination

**Réponse:**
```json
{
  "notifications": [...],
  "unreadCount": 5
}
```

### POST /api/notifications/{id}/read
Marquer une notification comme lue.

### POST /api/notifications/read-all
Marquer toutes les notifications comme lues.

## Modération

### POST /api/reports
Créer un rapport de modération.

**Body:**
```json
{
  "targetUserId": 2,
  "reason": "spam",
  "details": "Description du problème"
}
```

## Admin

### GET /api/admin/reports
Liste des rapports en attente (ROLE_ADMIN requis).

### GET /api/admin/reports/{id}
Détails d'un rapport.

### POST /api/admin/reports/{id}/review
Revoir un rapport.

**Body:**
```json
{
  "status": "action_taken",
  "notes": "Action prise"
}
```

### POST /api/admin/users/{id}/ban
Bannir un utilisateur.

**Body:**
```json
{
  "until": "2024-12-31T23:59:59Z" // optionnel
}
```

### POST /api/admin/users/{id}/unban
Débannir un utilisateur.

## Système de XP et Niveaux

- **Match créé**: +20 XP
- **Message envoyé**: +5 XP
- **Superlike**: +10 XP
- **Badge obtenu**: XP variable selon le badge
- **Quête complétée**: XP variable selon la quête

Le niveau est calculé automatiquement: `niveau = floor(XP / 100) + 1`

## Rôles

- `ROLE_USER`: Utilisateur standard
- `ROLE_ADMIN`: Administrateur (accès à `/api/admin/*`)
- `ROLE_MODERATOR`: Modérateur (à implémenter selon besoins)
- `ROLE_BANNED`: Utilisateur banni

## Codes d'erreur

- `400`: Bad Request - Données invalides
- `401`: Unauthorized - Token manquant ou invalide
- `403`: Forbidden - Accès refusé (permissions insuffisantes)
- `404`: Not Found - Ressource introuvable
- `409`: Conflict - Ressource déjà existante (ex: email déjà utilisé)
- `500`: Internal Server Error - Erreur serveur

## CORS

L'API accepte les requêtes depuis n'importe quelle origine. En production, il est recommandé de restreindre cela à votre domaine frontend.

## Rate Limiting

À implémenter selon les besoins avec Symfony RateLimiter component ou un middleware personnalisé.

