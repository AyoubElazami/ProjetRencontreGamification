# 🧪 Guide de Test Postman - API Profile

## 📋 Prérequis

1. **Base URL** : `http://localhost:8000`
2. **Format** : Toutes les requêtes JSON utilisent `Content-Type: application/json`
3. **Authentification** : Bearer Token JWT dans l'onglet Authorization

---

## 🔐 Étape 1 : Authentification

### 1.1. Inscription (Register)

**Méthode** : `POST`  
**URL** : `http://localhost:8000/api/register`

**Headers** :
```
Content-Type: application/json
```

**Body** (raw JSON) :
```json
{
  "email": "test@example.com",
  "password": "password123",
  "username": "TestUser"
}
```

**Réponse attendue** (201) :
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "user": {
    "id": 1,
    "email": "test@example.com",
    "username": "TestUser"
  }
}
```

**⚠️ IMPORTANT** : Copiez le `token` pour les prochaines requêtes !

---

### 1.2. Connexion (Login)

**Méthode** : `POST`  
**URL** : `http://localhost:8000/api/login`

**Headers** :
```
Content-Type: application/json
```

**Body** (raw JSON) :
```json
{
  "email": "test@example.com",
  "password": "password123"
}
```

**Réponse attendue** (200) :
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "user": {
    "id": 1,
    "email": "test@example.com",
    "username": "TestUser"
  }
}
```

**⚠️ IMPORTANT** : Copiez le `token` pour les prochaines requêtes !

---

## 👤 Étape 2 : Endpoints Profil (Authentification requise)

### 2.1. Message de Bienvenue

**Méthode** : `POST`  
**URL** : `http://localhost:8000/api/user/welcome`

**Authorization** :
- Type : `Bearer Token`
- Token : Collez le token obtenu lors du login/register

**Réponse attendue** (200) :
```json
{
  "message": "Bienvenue connecté !",
  "welcome": "Bienvenue TestUser !",
  "user": {
    "id": 1,
    "email": "test@example.com",
    "username": "TestUser"
  }
}
```

---

### 2.2. Récupérer mon Profil (GET /api/me)

**Méthode** : `GET`  
**URL** : `http://localhost:8000/api/me`

**Authorization** :
- Type : `Bearer Token`
- Token : Collez le token obtenu lors du login/register

**Réponse attendue** (200) :
```json
{
  "id": 1,
  "email": "test@example.com",
  "username": "TestUser",
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
  "createdAt": "2024-01-01T12:00:00+00:00",
  "updatedAt": "2024-01-01T12:00:00+00:00"
}
```

---

### 2.3. Mettre à jour mon Profil (PUT /api/me)

**Méthode** : `PUT` ou `PATCH`  
**URL** : `http://localhost:8000/api/me`

**Authorization** :
- Type : `Bearer Token`
- Token : Collez le token obtenu lors du login/register

**Headers** :
```
Content-Type: application/json
```

**Body** (raw JSON) - Exemple complet :
```json
{
  "username": "NouveauUsername",
  "bio": "Ma super bio !",
  "gender": "male",
  "age": 25,
  "location": "Paris, France",
  "latitude": "48.8566",
  "longitude": "2.3522",
  "preferences": {
    "age_range": [18, 35],
    "distance_km": 50,
    "gender_pref": "female"
  },
  "tags": ["sport", "musique", "cinema"]
}
```

**Body** (raw JSON) - Exemple partiel (mise à jour partielle) :
```json
{
  "bio": "Ma nouvelle bio",
  "age": 26
}
```

**Réponse attendue** (200) :
```json
{
  "id": 1,
  "email": "test@example.com",
  "username": "NouveauUsername",
  "bio": "Ma super bio !",
  "gender": "male",
  "age": 25,
  "location": "Paris, France",
  "latitude": "48.8566",
  "longitude": "2.3522",
  "preferences": {
    "age_range": [18, 35],
    "distance_km": 50,
    "gender_pref": "female"
  },
  "avatarUrl": null,
  "level": 1,
  "xp": 0,
  "score": 0,
  "tags": ["sport", "musique", "cinema"],
  "roles": ["ROLE_USER"],
  "createdAt": "2024-01-01T12:00:00+00:00",
  "updatedAt": "2024-01-01T12:05:00+00:00"
}
```

**Erreurs possibles** (400) :
```json
{
  "errors": [
    "email: This value is not a valid email address.",
    "age: This value should be between 18 and 100."
  ]
}
```

---

### 2.4. Upload Avatar (POST /api/me/avatar)

**Méthode** : `POST`  
**URL** : `http://localhost:8000/api/me/avatar`

**Authorization** :
- Type : `Bearer Token`
- Token : Collez le token obtenu lors du login/register

**Body** :
- Type : `form-data`
- Clé : `avatar` (type: File)
- Valeur : Sélectionnez un fichier image (jpg, png, etc.)

**⚠️ IMPORTANT** : 
- Ne pas utiliser `raw` ou `JSON`
- Utiliser l'onglet **Body** → **form-data**
- Sélectionner **File** pour la clé `avatar`

**Réponse attendue** (200) :
```json
{
  "avatarUrl": "/uploads/avatars/1/image-1234567890.jpg"
}
```

**Erreur si aucun fichier** (400) :
```json
{
  "error": "No file uploaded"
}
```

---

## ✅ Checklist de Test

### Tests d'Authentification
- [ ] ✅ Inscription réussie avec email valide
- [ ] ✅ Connexion réussie avec email/password corrects
- [ ] ✅ Erreur 401 avec mauvais credentials
- [ ] ✅ Erreur 400 avec email/password manquants

### Tests de Profil
- [ ] ✅ GET /api/me retourne le profil complet
- [ ] ✅ PUT /api/me met à jour tous les champs
- [ ] ✅ PUT /api/me met à jour partiellement (seulement certains champs)
- [ ] ✅ PUT /api/me avec tags crée les tags s'ils n'existent pas
- [ ] ✅ POST /api/user/welcome retourne le message de bienvenue
- [ ] ✅ POST /api/me/avatar upload un fichier image
- [ ] ✅ POST /api/me/avatar remplace l'ancien avatar

### Tests d'Erreurs
- [ ] ✅ Erreur 401 sans token
- [ ] ✅ Erreur 401 avec token invalide
- [ ] ✅ Erreur 400 avec données invalides (validation)
- [ ] ✅ Erreur 400 sans fichier pour l'avatar

---

## 🔍 Vérification du Token JWT

Pour vérifier que votre token est correct :

1. Allez sur **https://jwt.io**
2. Collez votre token dans la section "Encoded"
3. Vérifiez le **Payload** - il doit contenir :
```json
{
  "username": "test@example.com",  ← Doit être un EMAIL (pas "TestUser")
  "iat": 1234567890,
  "exp": 1234571490
}
```

**⚠️ Si vous voyez `"username": "TestUser"` au lieu d'un email, votre token est invalide. Reconnectez-vous !**

---

## 📝 Notes Importantes

1. **Token JWT** : Le token expire après un certain temps. Si vous recevez une erreur 401, reconnectez-vous pour obtenir un nouveau token.

2. **Tags** : Les tags sont créés automatiquement s'ils n'existent pas. Vous pouvez utiliser n'importe quel nom de tag.

3. **Avatar** : L'ancien avatar est automatiquement supprimé lors de l'upload d'un nouveau.

4. **Validation** : Les champs sont validés selon les contraintes de l'entité User. Vérifiez les erreurs dans la réponse 400.

5. **Base URL** : Assurez-vous que votre serveur Symfony tourne sur `http://localhost:8000` (ou modifiez l'URL selon votre configuration).

---

## 🚀 Collection Postman

Pour importer ces requêtes dans Postman :

1. Créez une nouvelle Collection : "Matchmaking API"
2. Créez un environnement avec la variable `base_url` = `http://localhost:8000`
3. Créez une variable `token` pour stocker votre token JWT
4. Dans chaque requête, utilisez `{{base_url}}/api/...` pour l'URL
5. Utilisez `{{token}}` dans l'Authorization Bearer Token

**Script Postman pour sauvegarder automatiquement le token** (dans la requête Login/Register) :
```javascript
if (pm.response.code === 200 || pm.response.code === 201) {
    var jsonData = pm.response.json();
    if (jsonData.token) {
        pm.environment.set("token", jsonData.token);
    }
}
```

---

**Bon test ! 🎉**

