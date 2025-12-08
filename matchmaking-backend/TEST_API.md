# Guide de test de l'API Matchmaking

## Configuration de base

**Base URL:** `http://localhost:8000` (ajuster selon votre configuration)

---

## 1. AUTHENTIFICATION

### 🔓 POST /api/register

**URL:** `http://localhost:8000/api/register`

**Headers:**
```
Content-Type: application/json
```

**Body:**
```json
{
  "email": "john.doe@example.com",
  "password": "password123",
  "username": "johndoe"
}
```

**Réponse attendue (201):**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "user": {
    "id": 1,
    "email": "john.doe@example.com",
    "username": "johndoe"
  }
}
```

**💾 SAUVEGARDER LE TOKEN pour les prochaines requêtes !**

---

### 🔓 POST /api/login

**URL:** `http://localhost:8000/api/login`

**Headers:**
```
Content-Type: application/json
```

**Body:**
```json
{
  "email": "john.doe@example.com",
  "password": "password123"
}
```

**Réponse attendue (200):**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "user": {
    "id": 1,
    "email": "john.doe@example.com",
    "username": "johndoe"
  }
}
```

---

## 2. PROFIL UTILISATEUR

### 🔐 GET /api/me

**URL:** `http://localhost:8000/api/me`

**Headers:**
```
Authorization: Bearer {VOTRE_TOKEN}
```

**Body:** Aucun

**Réponse attendue (200):**
```json
{
  "id": 1,
  "email": "john.doe@example.com",
  "username": "johndoe",
  "bio": null,
  "gender": null,
  "age": null,
  "location": null,
  "latitude": null,
  "longitude": null,
  "preferences": null,
  "avatarUrl": null,
  "level": 1,
  "xp": 0,
  "score": 0,
  "tags": [],
  "roles": ["ROLE_USER"],
  "createdAt": "2024-11-25T12:00:00+00:00",
  "updatedAt": null
}
```

---

### 🔐 PUT /api/me

**URL:** `http://localhost:8000/api/me`

**Headers:**
```
Authorization: Bearer {VOTRE_TOKEN}
Content-Type: application/json
```

**Body:**
```json
{
  "username": "john_updated",
  "bio": "Passionné de musique et de sport ! 🎵⚽",
  "gender": "male",
  "age": 28,
  "location": "Paris, France",
  "latitude": "48.8566",
  "longitude": "2.3522",
  "preferences": {
    "age_range": [22, 35],
    "distance_km": 50,
    "gender_pref": "female"
  },
  "tags": ["sport", "musique", "voyage"]
}
```

**Réponse attendue (200):** Profil mis à jour

---

### 🔐 POST /api/me/avatar

**URL:** `http://localhost:8000/api/me/avatar`

**Headers:**
```
Authorization: Bearer {VOTRE_TOKEN}
```

**Body (multipart/form-data):**
```
avatar: [SELECTIONNER UN FICHIER IMAGE]
```

**Réponse attendue (200):**
```json
{
  "avatarUrl": "/uploads/avatars/1/john-avatar-123456.jpg"
}
```

---

## 3. LISTE UTILISATEURS

### 🔐 GET /api/users

**URL:** `http://localhost:8000/api/users?page=1&limit=20`

**Headers:**
```
Authorization: Bearer {VOTRE_TOKEN}
```

**Body:** Aucun

**Réponse attendue (200):**
```json
{
  "users": [
    {
      "id": 2,
      "email": "jane.doe@example.com",
      "username": "janedoe",
      "bio": "Aventure et découvertes 🌍",
      "gender": "female",
      "age": 25,
      "location": "Lyon, France",
      "avatarUrl": "/uploads/avatars/2/jane-avatar.jpg",
      "level": 3,
      "xp": 250,
      "score": 350,
      "createdAt": "2024-11-24T10:00:00+00:00"
    }
  ],
  "page": 1,
  "limit": 20
}
```

---

### 🔐 GET /api/users/{id}

**URL:** `http://localhost:8000/api/users/2`

**Headers:**
```
Authorization: Bearer {VOTRE_TOKEN}
```

**Body:** Aucun

---

### 🔐 GET /api/recommendations

**URL:** `http://localhost:8000/api/recommendations?limit=10`

**Headers:**
```
Authorization: Bearer {VOTRE_TOKEN}
```

**Body:** Aucun

**Réponse attendue (200):**
```json
{
  "users": [
    {
      "id": 2,
      "email": "jane.doe@example.com",
      "username": "janedoe",
      "bio": "Aventure et découvertes 🌍",
      "gender": "female",
      "age": 25,
      "location": "Lyon, France",
      "avatarUrl": "/uploads/avatars/2/jane-avatar.jpg",
      "level": 3,
      "xp": 250,
      "score": 350,
      "createdAt": "2024-11-24T10:00:00+00:00"
    }
  ]
}
```

---

## 4. MATCHMAKING / INTERACTIONS

### 🔐 POST /api/users/{id}/like

**URL:** `http://localhost:8000/api/users/2/like`

**Headers:**
```
Authorization: Bearer {VOTRE_TOKEN}
Content-Type: application/json
```

**Body:** Aucun (ou vide `{}`)

**Réponse attendue (201):**
```json
{
  "success": true,
  "matchRequest": {
    "id": 1,
    "type": "like",
    "status": "pending"
  }
}
```

**Si l'autre utilisateur vous a déjà liké, vous recevrez un match créé automatiquement !**

---

### 🔐 POST /api/users/{id}/dislike

**URL:** `http://localhost:8000/api/users/2/dislike`

**Headers:**
```
Authorization: Bearer {VOTRE_TOKEN}
Content-Type: application/json
```

**Body:** Aucun (ou vide `{}`)

---

### 🔐 POST /api/users/{id}/superlike

**URL:** `http://localhost:8000/api/users/2/superlike`

**Headers:**
```
Authorization: Bearer {VOTRE_TOKEN}
Content-Type: application/json
```

**Body:** Aucun (ou vide `{}`)

**Réponse attendue (201):**
```json
{
  "success": true,
  "matchRequest": {
    "id": 2,
    "type": "superlike",
    "status": "pending"
  }
}
```

**💡 Vous gagnez +10 XP avec un superlike !**

---

### 🔐 POST /api/users/{id}/wink

**URL:** `http://localhost:8000/api/users/2/wink`

**Headers:**
```
Authorization: Bearer {VOTRE_TOKEN}
Content-Type: application/json
```

**Body:** Aucun (ou vide `{}`)

---

## 5. MATCHES

### 🔐 GET /api/matches

**URL:** `http://localhost:8000/api/matches`

**Headers:**
```
Authorization: Bearer {VOTRE_TOKEN}
```

**Body:** Aucun

**Réponse attendue (200):**
```json
{
  "matches": [
    {
      "id": 1,
      "user": {
        "id": 2,
        "username": "janedoe",
        "avatarUrl": "/uploads/avatars/2/jane-avatar.jpg",
        "bio": "Aventure et découvertes 🌍"
      },
      "createdAt": "2024-11-25T14:30:00+00:00",
      "lastInteractionAt": "2024-11-25T14:30:00+00:00"
    }
  ]
}
```

---

### 🔐 GET /api/matches/{id}

**URL:** `http://localhost:8000/api/matches/1`

**Headers:**
```
Authorization: Bearer {VOTRE_TOKEN}
```

**Body:** Aucun

---

### 🔐 GET /api/matches/{id}/messages

**URL:** `http://localhost:8000/api/matches/1/messages?limit=50&offset=0`

**Headers:**
```
Authorization: Bearer {VOTRE_TOKEN}
```

**Body:** Aucun

**Réponse attendue (200):**
```json
{
  "messages": [
    {
      "id": 1,
      "senderId": 1,
      "content": "Salut ! Comment ça va ?",
      "readAt": null,
      "createdAt": "2024-11-25T15:00:00+00:00"
    },
    {
      "id": 2,
      "senderId": 2,
      "content": "Salut ! Ça va très bien, merci ! 😊",
      "readAt": null,
      "createdAt": "2024-11-25T15:05:00+00:00"
    }
  ]
}
```

---

### 🔐 POST /api/matches/{id}/message

**URL:** `http://localhost:8000/api/matches/1/message`

**Headers:**
```
Authorization: Bearer {VOTRE_TOKEN}
Content-Type: application/json
```

**Body:**
```json
{
  "content": "Salut ! Comment ça va ? 😊"
}
```

**Réponse attendue (201):**
```json
{
  "id": 1,
  "senderId": 1,
  "content": "Salut ! Comment ça va ? 😊",
  "readAt": null,
  "createdAt": "2024-11-25T15:00:00+00:00"
}
```

**💡 Vous gagnez +5 XP par message envoyé !**

---

## 6. GAMIFICATION

### 🔐 GET /api/me/gamification

**URL:** `http://localhost:8000/api/me/gamification`

**Headers:**
```
Authorization: Bearer {VOTRE_TOKEN}
```

**Body:** Aucun

**Réponse attendue (200):**
```json
{
  "level": 3,
  "xp": 275,
  "score": 450,
  "badges": [
    {
      "code": "level_5",
      "name": "Niveau 5",
      "description": "Atteint le niveau 5",
      "icon": "badge_level_5.png",
      "awardedAt": "2024-11-25T16:00:00+00:00"
    }
  ],
  "quests": [
    {
      "code": "first_match",
      "title": "Premier match",
      "description": "Faites votre premier match",
      "xpReward": 50,
      "progress": {
        "matches": 1
      },
      "completedAt": "2024-11-25T14:30:00+00:00",
      "isCompleted": true
    }
  ]
}
```

---

### 🔐 GET /api/leaderboard

**URL:** `http://localhost:8000/api/leaderboard?limit=10`

**Headers:**
```
Authorization: Bearer {VOTRE_TOKEN}
```

**Body:** Aucun

**Réponse attendue (200):**
```json
{
  "leaderboard": [
    {
      "rank": 1,
      "user": {
        "id": 5,
        "username": "champion",
        "avatarUrl": "/uploads/avatars/5/champion.jpg",
        "level": 10,
        "xp": 950,
        "score": 1200
      }
    },
    {
      "rank": 2,
      "user": {
        "id": 3,
        "username": "player2",
        "avatarUrl": "/uploads/avatars/3/player2.jpg",
        "level": 8,
        "xp": 750,
        "score": 900
      }
    }
  ]
}
```

---

## 7. NOTIFICATIONS

### 🔐 GET /api/notifications

**URL:** `http://localhost:8000/api/notifications?limit=50&offset=0`

**Headers:**
```
Authorization: Bearer {VOTRE_TOKEN}
```

**Body:** Aucun

**Réponse attendue (200):**
```json
{
  "notifications": [
    {
      "id": 1,
      "type": "match",
      "payload": {
        "matchId": 1,
        "userId": 2,
        "username": "janedoe",
        "avatarUrl": "/uploads/avatars/2/jane-avatar.jpg"
      },
      "readAt": null,
      "createdAt": "2024-11-25T14:30:00+00:00",
      "isRead": false
    },
    {
      "id": 2,
      "type": "message",
      "payload": {
        "matchId": 1,
        "senderId": 2,
        "username": "janedoe",
        "content": "Salut !"
      },
      "readAt": null,
      "createdAt": "2024-11-25T15:05:00+00:00",
      "isRead": false
    }
  ],
  "unreadCount": 2
}
```

---

### 🔐 POST /api/notifications/{id}/read

**URL:** `http://localhost:8000/api/notifications/1/read`

**Headers:**
```
Authorization: Bearer {VOTRE_TOKEN}
Content-Type: application/json
```

**Body:** Aucun (ou vide `{}`)

**Réponse attendue (200):**
```json
{
  "success": true
}
```

---

### 🔐 POST /api/notifications/read-all

**URL:** `http://localhost:8000/api/notifications/read-all`

**Headers:**
```
Authorization: Bearer {VOTRE_TOKEN}
Content-Type: application/json
```

**Body:** Aucun (ou vide `{}`)

**Réponse attendue (200):**
```json
{
  "success": true
}
```

---

## 8. MODÉRATION

### 🔐 POST /api/reports

**URL:** `http://localhost:8000/api/reports`

**Headers:**
```
Authorization: Bearer {VOTRE_TOKEN}
Content-Type: application/json
```

**Body:**
```json
{
  "targetUserId": 2,
  "reason": "spam",
  "details": "Cet utilisateur envoie des messages répétitifs et indésirables"
}
```

**Réponse attendue (201):**
```json
{
  "success": true,
  "report": {
    "id": 1,
    "status": "pending"
  }
}
```

---

## 9. ADMIN (ROLE_ADMIN requis)

### 🔐 GET /api/admin/reports

**URL:** `http://localhost:8000/api/admin/reports`

**Headers:**
```
Authorization: Bearer {TOKEN_ADMIN}
```

**Body:** Aucun

---

### 🔐 GET /api/admin/reports/{id}

**URL:** `http://localhost:8000/api/admin/reports/1`

**Headers:**
```
Authorization: Bearer {TOKEN_ADMIN}
```

**Body:** Aucun

---

### 🔐 POST /api/admin/reports/{id}/review

**URL:** `http://localhost:8000/api/admin/reports/1/review`

**Headers:**
```
Authorization: Bearer {TOKEN_ADMIN}
Content-Type: application/json
```

**Body:**
```json
{
  "status": "action_taken",
  "notes": "Utilisateur banni temporairement pour 7 jours"
}
```

**Status possibles:** `"reviewed"`, `"action_taken"`, `"dismissed"`

---

### 🔐 POST /api/admin/users/{id}/ban

**URL:** `http://localhost:8000/api/admin/users/2/ban`

**Headers:**
```
Authorization: Bearer {TOKEN_ADMIN}
Content-Type: application/json
```

**Body (optionnel):**
```json
{
  "until": "2024-12-01T00:00:00Z"
}
```

**Ou bannissement permanent (pas de champ "until"):**
```json
{}
```

---

### 🔐 POST /api/admin/users/{id}/unban

**URL:** `http://localhost:8000/api/admin/users/2/unban`

**Headers:**
```
Authorization: Bearer {TOKEN_ADMIN}
Content-Type: application/json
```

**Body:** Aucun (ou vide `{}`)

---

## 🔄 SÉQUENCE DE TEST COMPLÈTE

### 1. Créer deux utilisateurs

**User 1:**
```bash
POST http://localhost:8000/api/register
{
  "email": "user1@test.com",
  "password": "test123",
  "username": "user1"
}
→ Sauvegarder TOKEN1
```

**User 2:**
```bash
POST http://localhost:8000/api/register
{
  "email": "user2@test.com",
  "password": "test123",
  "username": "user2"
}
→ Sauvegarder TOKEN2
```

### 2. Compléter les profils

**User 1 avec TOKEN1:**
```bash
PUT http://localhost:8000/api/me
{
  "gender": "male",
  "age": 28,
  "bio": "J'aime la musique et le sport"
}
```

**User 2 avec TOKEN2:**
```bash
PUT http://localhost:8000/api/me
{
  "gender": "female",
  "age": 25,
  "bio": "Passionnée de voyage"
}
```

### 3. User 1 like User 2

**Avec TOKEN1:**
```bash
POST http://localhost:8000/api/users/2/like
```

### 4. User 2 like User 1 (création automatique d'un match !)

**Avec TOKEN2:**
```bash
POST http://localhost:8000/api/users/1/like
```

### 5. Vérifier les matches

**Avec TOKEN1:**
```bash
GET http://localhost:8000/api/matches
```

### 6. Envoyer un message

**Avec TOKEN1:**
```bash
POST http://localhost:8000/api/matches/1/message
{
  "content": "Salut ! On s'est matché ! 😊"
}
```

### 7. Vérifier les notifications

**Avec TOKEN2:**
```bash
GET http://localhost:8000/api/notifications
```

### 8. Vérifier la gamification

**Avec TOKEN1:**
```bash
GET http://localhost:8000/api/me/gamification
```

---

## 📝 NOTES IMPORTANTES

- **Remplacer `{VOTRE_TOKEN}`** par le token reçu lors du login/register
- **Remplacer `{id}`** par l'ID réel de l'utilisateur/match/notification
- **Base URL** : Ajustez selon votre configuration (`http://localhost:8000`, `http://127.0.0.1:8000`, etc.)
- **Pour les routes admin** : Vous devez avoir le rôle `ROLE_ADMIN`
- **Les IDs** sont générés automatiquement, vérifiez-les après chaque création

---

## 🛠️ OUTILS DE TEST

### cURL

Exemple de login:
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john.doe@example.com","password":"password123"}'
```

### Postman

1. Créer une collection "Matchmaking API"
2. Ajouter une variable d'environnement `base_url` = `http://localhost:8000`
3. Ajouter une variable `token` pour stocker le JWT
4. Créer des requêtes avec `{{base_url}}/api/...`

### Insomnia / Thunder Client

Même principe que Postman, créez une collection et utilisez les variables.

---

Bon test ! 🚀

