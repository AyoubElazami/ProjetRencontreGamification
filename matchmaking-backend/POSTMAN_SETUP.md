# Guide Postman - Configuration de l'authentification JWT

## 🔐 Configuration de l'Authorization dans Postman

### Méthode 1 : Configuration manuelle dans chaque requête

1. **Sélectionner votre requête** dans Postman
2. **Aller dans l'onglet "Authorization"**
3. **Dans "Type"**, sélectionner **"Bearer Token"**
4. **Dans le champ "Token"**, coller votre JWT token (sans le préfixe "Bearer")
5. **Cliquer sur "Send"**

### Méthode 2 : Utiliser une variable d'environnement (RECOMMANDÉ)

Cette méthode vous permet de stocker le token une fois et de l'utiliser dans toutes vos requêtes.

#### Étape 1 : Créer un environnement

1. Cliquez sur **"Environments"** en haut à droite ou sur l'icône ⚙️
2. Cliquez sur **"+"** pour créer un nouvel environnement
3. Nommez-le : `Matchmaking API` (ou autre nom)
4. Ajoutez une variable :
   - **Variable name:** `token`
   - **Initial value:** (laissez vide)
   - **Current value:** (laissez vide)
5. Cliquez sur **"Save"**

#### Étape 2 : Configurer l'autorisation dans l'environnement

1. Dans votre requête, allez dans l'onglet **"Authorization"**
2. Sélectionnez **"Bearer Token"** comme Type
3. Dans le champ Token, tapez : `{{token}}`
4. Postman utilisera automatiquement la variable `token` de votre environnement

#### Étape 3 : Stocker automatiquement le token après login

Créez un script dans votre requête de **login** pour stocker automatiquement le token :

1. Allez dans votre requête **POST /api/login**
2. Cliquez sur l'onglet **"Tests"** (en dessous de l'URL)
3. Ajoutez ce script :

```javascript
// Extraire le token de la réponse et le stocker dans la variable d'environnement
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    if (jsonData.token) {
        pm.environment.set("token", jsonData.token);
        console.log("Token sauvegardé avec succès !");
    }
}
```

4. Maintenant, chaque fois que vous vous connectez, le token sera automatiquement sauvegardé !

---

## 📋 Étapes complètes pour tester l'API

### 1. Créer un compte (Register)

**Requête:** `POST http://localhost:8000/api/register`

**Body (raw JSON):**
```json
{
  "email": "test@example.com",
  "password": "password123",
  "username": "testuser"
}
```

**Réponse attendue:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "user": {
    "id": 1,
    "email": "test@example.com",
    "username": "testuser"
  }
}
```

**📋 Action :** Copiez le `token` de la réponse !

### 2. Configurer l'autorisation

#### Option A : Dans chaque requête manuellement

1. Ouvrez une nouvelle requête (ex: `GET /api/me`)
2. Onglet **"Authorization"**
3. Type : **"Bearer Token"**
4. Token : Collez le token que vous avez copié
   ```
   eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...
   ```

#### Option B : Avec variable d'environnement

1. Dans votre requête de **login/register**, onglet **"Tests"**, ajoutez :
```javascript
if (pm.response.code === 200 || pm.response.code === 201) {
    var jsonData = pm.response.json();
    if (jsonData.token) {
        pm.environment.set("token", jsonData.token);
    }
}
```

2. Dans vos autres requêtes, onglet **"Authorization"** :
   - Type : **"Bearer Token"**
   - Token : `{{token}}`

### 3. Tester une route protégée

**Requête:** `GET http://localhost:8000/api/me`

**Headers:**
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...
```

Ou si vous utilisez l'onglet Authorization de Postman avec Bearer Token, Postman ajoutera automatiquement ce header.

---

## 🎯 Exemple complet : Collection Postman

### Structure recommandée

1. **Auth**
   - `POST /api/register` (avec script pour sauvegarder le token)
   - `POST /api/login` (avec script pour sauvegarder le token)

2. **User/Profile**
   - `GET /api/me`
   - `PUT /api/me`
   - `POST /api/me/avatar`
   - `GET /api/users`
   - `GET /api/users/{id}`

3. **Matchmaking**
   - `POST /api/users/{id}/like`
   - `GET /api/matches`
   - etc.

### Configuration de l'environnement Postman

**Variables d'environnement recommandées :**

| Variable | Valeur initiale | Description |
|----------|----------------|-------------|
| `base_url` | `http://localhost:8000` | URL de base de l'API |
| `token` | (vide) | Token JWT (sauvegardé automatiquement) |
| `user_id` | (vide) | ID de l'utilisateur connecté |

### Script automatique pour login/register

Dans l'onglet **"Tests"** de vos requêtes de login/register :

```javascript
// Vérifier que la réponse est OK
pm.test("Status code is 200 or 201", function () {
    pm.expect(pm.response.code).to.be.oneOf([200, 201]);
});

// Sauvegarder le token automatiquement
if (pm.response.code === 200 || pm.response.code === 201) {
    var jsonData = pm.response.json();
    
    if (jsonData.token) {
        pm.environment.set("token", jsonData.token);
        console.log("✅ Token sauvegardé : " + jsonData.token.substring(0, 20) + "...");
    }
    
    if (jsonData.user && jsonData.user.id) {
        pm.environment.set("user_id", jsonData.user.id);
        console.log("✅ User ID sauvegardé : " + jsonData.user.id);
    }
}
```

---

## 🔧 Format du header Authorization

Le format correct est :

```
Authorization: Bearer {VOTRE_TOKEN_JWT}
```

**Important :**
- Il y a un espace entre `Bearer` et le token
- Pas de guillemets autour du token
- Le token doit être complet (souvent très long)

---

## ✅ Vérification rapide

Pour vérifier que votre token est bien envoyé :

1. Dans Postman, ouvrez l'onglet **"Headers"** de votre requête
2. Vous devriez voir :
   ```
   Key: Authorization
   Value: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...
   ```

Si vous ne voyez pas ce header, votre configuration Authorization n'est pas correcte.

---

## 🐛 Dépannage

### Erreur "Invalid token"

**Causes possibles :**

1. **Token expiré** → Connectez-vous à nouveau
2. **Token mal formaté** → Vérifiez qu'il n'y a pas d'espaces supplémentaires
3. **Token incomplet** → Le token a été tronqué lors du copier-coller
4. **Header manquant** → Vérifiez que l'onglet Authorization est bien configuré

### Solution rapide

1. Videz le cache Postman (Settings → Clear cache)
2. Reconnectez-vous et obtenez un nouveau token
3. Vérifiez le format dans l'onglet Headers

---

## 📸 Capture d'écran des étapes

### Étape 1 : Onglet Authorization

```
┌─────────────────────────────────┐
│ Type: [Bearer Token ▼]          │
│ Token: [{{token}}____________]  │
└─────────────────────────────────┘
```

### Étape 2 : Onglet Headers (vérification)

```
┌─────────────────┬──────────────────────────────────────┐
│ Key             │ Value                                │
├─────────────────┼──────────────────────────────────────┤
│ Authorization   │ Bearer eyJ0eXAiOiJKV1QiLCJhbG...    │
└─────────────────┴──────────────────────────────────────┘
```

---

## 🚀 Exemple de requête complète

### GET /api/me

**Method:** `GET`

**URL:** `{{base_url}}/api/me`

**Authorization:**
- Type: `Bearer Token`
- Token: `{{token}}`

**Headers:**
```
Accept: application/json
Content-Type: application/json
```

**Body:** Aucun

**Tests (optionnel):**
```javascript
pm.test("Status code is 200", function () {
    pm.response.to.have.status(200);
});

pm.test("Response has user data", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property('id');
    pm.expect(jsonData).to.have.property('email');
});
```

---

Bon test ! 🎉

