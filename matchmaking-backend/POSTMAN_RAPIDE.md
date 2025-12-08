# ⚡ Postman - Configuration rapide

## En 3 étapes

### 1️⃣ Obtenir un token

**POST** `http://localhost:8000/api/register`

**Body (raw JSON):**
```json
{
  "email": "test@example.com",
  "password": "password123",
  "username": "testuser"
}
```

**→ Copiez le `token` de la réponse !**

---

### 2️⃣ Configurer l'autorisation

Dans votre requête (ex: `GET /api/me`) :

1. Onglet **"Authorization"**
2. Type : **"Bearer Token"**
3. Token : Collez votre token
4. **C'est tout !** ✅

---

### 3️⃣ Tester

**GET** `http://localhost:8000/api/me`

**→ Vous devriez recevoir votre profil utilisateur !**

---

## Format du Header automatique

Postman génère :
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...
```

---

## Si erreur "Bad credentials"

1. Videz le cache : `php bin/console cache:clear`
2. Reconnectez-vous pour obtenir un nouveau token
3. Vérifiez que le token est complet (très long)

---

C'est tout ! 🎉

