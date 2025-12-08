# 👋 Route POST de bienvenue

## Route créée

**POST** `/api/user/welcome`

Cette route affiche un message de bienvenue pour l'utilisateur connecté.

## Comment tester

### 1. Obtenir un token

**POST** `http://localhost:8000/api/login`

**Body (raw JSON):**
```json
{
  "email": "test@example.com",
  "password": "votre_password"
}
```

**→ Copiez le `token`**

### 2. Appeler la route de bienvenue

**POST** `http://localhost:8000/api/user/welcome`

**Dans Postman :**
1. Onglet **Authorization**
2. Type : **Bearer Token**
3. Token : Collez votre token

**Réponse attendue :**
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

## Détails de la route

- **Méthode** : POST
- **URL** : `/api/user/welcome`
- **Authentification** : Requis (Bearer Token)
- **Rôle requis** : `ROLE_USER`

---

Testez maintenant ! 🚀

