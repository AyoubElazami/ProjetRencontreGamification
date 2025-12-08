# 🚀 Guide Postman - Configuration rapide

## 📋 Configuration de l'Authorization dans Postman

### Méthode simple (étape par étape)

#### 1. Obtenir un token

**POST** `http://localhost:8000/api/register`

**Body (raw JSON):**
```json
{
  "email": "test@example.com",
  "password": "password123",
  "username": "testuser"
}
```

**Réponse:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1c2VyX2lkIjoxLCJlbWFpbCI6InRlc3RAZXhhbXBsZS5jb20ifQ...",
  "user": {...}
}
```

**⚠️ IMPORTANT :** Copiez le token complet de la réponse !

---

#### 2. Configurer l'autorisation dans Postman

1. **Ouvrez votre requête** (ex: `GET /api/me`)
2. **Cliquez sur l'onglet "Authorization"** (en dessous de l'URL)
3. **Dans "Type"**, sélectionnez **"Bearer Token"**
4. **Dans le champ "Token"**, collez votre token (sans "Bearer", juste le token)
5. **C'est tout !** Postman ajoutera automatiquement le header `Authorization: Bearer {token}`

---

### ✅ Format correct

Postman génère automatiquement ce header :
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...
```

**Important :**
- ✅ Il y a un espace entre `Bearer` et le token
- ✅ Pas de guillemets
- ✅ Le token est complet (très long, souvent 200+ caractères)

---

### 🔍 Vérifier que le header est bien envoyé

1. Cliquez sur l'onglet **"Headers"**
2. Vous devriez voir :
   ```
   Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...
   ```

Si vous ne voyez pas ce header, retournez dans l'onglet Authorization et vérifiez la configuration.

---

## 🎯 Exemple complet : Test de GET /api/me

### Étape 1 : Login/Register

**POST** `http://localhost:8000/api/login`

**Body:**
```json
{
  "email": "test@example.com",
  "password": "password123"
}
```

**Réponse:** Copiez le `token`

---

### Étape 2 : Requête protégée

**GET** `http://localhost:8000/api/me`

**Configuration Authorization:**
- Type: `Bearer Token`
- Token: `eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...` (votre token)

**Send** → Vous devriez recevoir votre profil utilisateur !

---

## 💡 Astuce : Utiliser des variables d'environnement

### Configuration automatique avec variables

#### 1. Créer un environnement

1. Cliquez sur l'icône ⚙️ (ou "Environments") en haut à droite
2. Cliquez sur **"+"** pour créer un environnement
3. Nommez-le : `Matchmaking API`
4. Ajoutez ces variables :
   - `base_url` = `http://localhost:8000`
   - `token` = (laissez vide pour l'instant)
5. Cliquez sur **"Save"**
6. **Sélectionnez cet environnement** dans le menu déroulant en haut à droite

#### 2. Sauvegarder automatiquement le token après login

Dans votre requête **POST /api/login**, allez dans l'onglet **"Tests"** et ajoutez :

```javascript
if (pm.response.code === 200 || pm.response.code === 201) {
    var jsonData = pm.response.json();
    if (jsonData.token) {
        pm.environment.set("token", jsonData.token);
        console.log("✅ Token sauvegardé !");
    }
}
```

#### 3. Utiliser la variable dans vos requêtes

Dans l'onglet **"Authorization"** de vos requêtes protégées :
- Type: `Bearer Token`
- Token: `{{token}}`

Maintenant, après chaque login, le token sera automatiquement utilisé dans toutes vos requêtes !

---

## 🐛 Dépannage : Erreur "Invalid token"

### Vérifications

1. **Token expiré ?**
   - Les tokens JWT peuvent expirer
   - Solution : Reconnectez-vous pour obtenir un nouveau token

2. **Token mal formaté ?**
   - Vérifiez qu'il n'y a pas d'espaces supplémentaires
   - Vérifiez qu'il n'y a pas de guillemets autour du token
   - Le token doit commencer par `eyJ`

3. **Token incomplet ?**
   - Assurez-vous d'avoir copié le token complet
   - Les tokens JWT sont très longs (souvent 200+ caractères)

4. **Header manquant ?**
   - Vérifiez dans l'onglet "Headers" que `Authorization` est présent
   - Format : `Bearer {token}`

### Test rapide

Testez avec cette requête pour voir le token décodé :

**GET** `http://localhost:8000/api/me`

Si ça ne fonctionne pas :
1. Vérifiez que le token est bien dans l'onglet Headers
2. Essayez de vous reconnecter et obtenez un nouveau token
3. Vérifiez que l'URL de base est correcte (`http://localhost:8000`)

---

## 📸 Résumé visuel

```
┌─────────────────────────────────────────────┐
│ POSTMAN                                     │
├─────────────────────────────────────────────┤
│                                             │
│ GET http://localhost:8000/api/me            │
│                                             │
│ [Params] [Authorization] [Headers] [Body]  │
│                                             │
│ ┌─ Authorization ───────────────────────┐  │
│ │ Type: [Bearer Token ▼]                │  │
│ │ Token: [eyJ0eXAiOiJKV1QiLCJhbGc...]  │  │
│ └───────────────────────────────────────┘  │
│                                             │
│ [Send]                                      │
└─────────────────────────────────────────────┘
```

---

## ✅ Checklist

- [ ] Token obtenu via `/api/register` ou `/api/login`
- [ ] Onglet Authorization configuré avec "Bearer Token"
- [ ] Token collé dans le champ Token
- [ ] Header `Authorization: Bearer {token}` visible dans l'onglet Headers
- [ ] Requête envoyée avec succès !

---

Bon test ! 🎉

