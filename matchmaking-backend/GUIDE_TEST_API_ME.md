# 🧪 Guide de test pour `/api/me`

## Problème actuel

Vous recevez :
```json
{
    "code": 401,
    "message": "Invalid credentials."
}
```

## ✅ Solution étape par étape

### 1. Obtenir un token valide

**POST** `http://localhost:8000/api/login`

**Body (raw JSON):**
```json
{
  "email": "votre_email@example.com",
  "password": "votre_password"
}
```

**Réponse attendue :**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "user": {
    "id": 1,
    "email": "votre_email@example.com",
    "username": "..."
  }
}
```

**→ COPIEZ LE TOKEN !**

---

### 2. Vérifier le contenu du token

Allez sur **https://jwt.io** et collez votre token.

**Le payload doit contenir :**
```json
{
  "username": "votre_email@example.com",
  "iat": 1234567890,
  "exp": 1234571490
}
```

**Important :** Le champ `"username"` doit contenir votre **email**.

---

### 3. Tester `/api/me`

**GET** `http://localhost:8000/api/me`

**Dans Postman :**
1. Onglet **Authorization**
2. Type : **Bearer Token**
3. Token : Collez le token que vous avez copié à l'étape 1

---

### 4. Messages d'erreur possibles

#### ✅ Si ça fonctionne :
```json
{
  "id": 1,
  "email": "votre_email@example.com",
  "username": "...",
  ...
}
```

#### ❌ Si erreur :

**A. "No token provided"**
- Le header `Authorization` n'est pas présent
- Vérifiez que vous avez configuré **Bearer Token** dans Postman

**B. "Invalid token format"**
- Le token n'a pas 3 parties (header.payload.signature)
- Reconnectez-vous pour obtenir un nouveau token

**C. "Invalid token: no username in payload"**
- Le payload ne contient pas `username`
- Vérifiez sur jwt.io ce que contient le token

**D. "User not found: email@example.com"**
- L'utilisateur avec cet email n'existe pas dans la base de données
- Vérifiez que l'email dans le token correspond à un utilisateur enregistré

**E. "Invalid credentials"**
- Cela peut venir de Symfony Security
- Videz le cache : `php bin/console cache:clear`
- Reconnectez-vous pour obtenir un nouveau token

---

### 5. Vérifications supplémentaires

**Vider le cache :**
```bash
php bin/console cache:clear
```

**Vérifier que l'utilisateur existe en base :**
```bash
php bin/console doctrine:query:sql "SELECT id, email FROM user"
```

---

## 🎯 Checklist

- [ ] Token obtenu avec succès lors du login
- [ ] Token décodé sur jwt.io montre `username` avec l'email
- [ ] Bearer Token configuré dans Postman
- [ ] Cache vidé (`php bin/console cache:clear`)
- [ ] L'utilisateur existe dans la base de données

---

**Si le problème persiste, partagez le message d'erreur exact que vous recevez !** 🔍

