# 🔐 Configuration Postman - Autorisation JWT

## ⚡ Méthode rapide (2 minutes)

### Étape 1 : Obtenir un token

**POST** `http://localhost:8000/api/register`

**Body (raw JSON):**
```json
{
  "email": "test@example.com",
  "password": "password123",
  "username": "testuser"
}
```

**Copiez le `token` de la réponse !**

---

### Étape 2 : Configurer l'autorisation dans Postman

1. **Ouvrez votre requête** (ex: `GET /api/me`)
2. **Cliquez sur l'onglet "Authorization"** (sous l'URL)
3. **Type** : Sélectionnez **"Bearer Token"**
4. **Token** : Collez votre token (sans "Bearer", juste le token)
5. **C'est tout !** ✅

---

## 📋 Format du Header

Postman génère automatiquement :
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...
```

---

## ✅ Vérification

Dans l'onglet **"Headers"**, vous devriez voir :
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...
```

---

## 🎯 Exemple complet

### 1. Register → Obtenir token

**POST** `http://localhost:8000/api/register`
```json
{
  "email": "user1@test.com",
  "password": "test123",
  "username": "user1"
}
```

**Réponse :**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "user": {...}
}
```

### 2. Utiliser le token

**GET** `http://localhost:8000/api/me`

**Onglet Authorization :**
- Type: `Bearer Token`
- Token: `eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...` (votre token)

**Send** → Vous recevez votre profil ! ✅

---

## 🐛 Si "Invalid token"

1. **Vérifiez** que le token est complet (très long, commence par `eyJ`)
2. **Reconnectez-vous** pour obtenir un nouveau token
3. **Vérifiez** dans l'onglet Headers que `Authorization: Bearer {token}` est présent

---

## 💡 Astuce : Variable d'environnement

Pour ne pas copier-coller le token à chaque fois :

1. Créez un environnement avec variable `token`
2. Dans votre requête de login, onglet **"Tests"**, ajoutez :
```javascript
if (pm.response.code === 200 || pm.response.code === 201) {
    var jsonData = pm.response.json();
    if (jsonData.token) {
        pm.environment.set("token", jsonData.token);
    }
}
```

3. Dans vos autres requêtes, onglet **"Authorization"** :
   - Token: `{{token}}`

---

C'est tout ! 🎉

