# 🔍 Diagnostic : Erreur "User not found: TestUser5"

## ❌ Le Problème

Vous recevez cette erreur :
```json
{
    "code": 401,
    "message": "User not found: TestUser5"
}
```

Cela signifie que votre **token JWT contient "TestUser5"** dans le champ `username` du payload, mais le système cherche un utilisateur avec l'**email** "TestUser5", qui n'existe probablement pas.

---

## 🔎 Étape 1 : Vérifier votre token sur jwt.io

1. Allez sur **https://jwt.io**
2. Collez votre token dans la section "Encoded"
3. Regardez le **Payload (Decoded)**

**Si vous voyez :**
```json
{
  "username": "TestUser5",  ← ❌ PROBLÈME
  "iat": 1234567890,
  "exp": 1234571490
}
```

Cela signifie que vous vous êtes **inscrit ou connecté avec l'email "TestUser5"** au lieu d'un email valide.

---

## 🔎 Étape 2 : Vérifier les utilisateurs en base de données

Exécutez cette commande pour voir tous les utilisateurs :

```bash
php bin/console doctrine:query:sql "SELECT id, email, username FROM \`user\`"
```

**Résultat attendu :**
```
id | email                | username
---|----------------------|----------
1  | test@example.com      | TestUser
2  | user@example.com      | TestUser5
```

**Si vous voyez :**
```
id | email      | username
---|------------|----------
1  | TestUser5  | TestUser5  ← ❌ PROBLÈME : email invalide
```

Cela confirme que vous avez créé un compte avec "TestUser5" comme email (ce qui est invalide).

---

## ✅ Solution : Créer un compte avec un email valide

### Option A : S'inscrire avec un email valide

**POST** `http://localhost:8000/api/register`

**Body (raw JSON) :**
```json
{
  "email": "testuser5@example.com",  ← ✅ Email VALIDE (avec @)
  "password": "password123",
  "username": "TestUser5"  ← Le username peut être "TestUser5"
}
```

**Réponse attendue :**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "user": {
    "id": 3,
    "email": "testuser5@example.com",
    "username": "TestUser5"
  }
}
```

**⚠️ IMPORTANT :** Copiez le nouveau token et vérifiez-le sur jwt.io. Le payload devrait maintenant contenir :
```json
{
  "username": "testuser5@example.com",  ← ✅ Email valide
  "iat": 1234567890,
  "exp": 1234571490
}
```

---

### Option B : Se connecter avec un compte existant (si vous avez un email valide)

**POST** `http://localhost:8000/api/login`

**Body (raw JSON) :**
```json
{
  "email": "votre_email_valide@example.com",  ← Email qui existe en base
  "password": "votre_password"
}
```

---

## 🧹 Nettoyer les comptes invalides (Optionnel)

Si vous avez créé des comptes avec des emails invalides, vous pouvez les supprimer :

```bash
php bin/console doctrine:query:sql "DELETE FROM \`user\` WHERE email NOT LIKE '%@%'"
```

Cette commande supprime tous les utilisateurs dont l'email ne contient pas "@" (donc invalides).

---

## 📝 Pourquoi ce problème ?

1. **LexikJWTBundle** stocke `getUserIdentifier()` dans le champ `"username"` du token JWT
2. **`getUserIdentifier()`** retourne l'**email** de l'utilisateur (voir `User.php` ligne 138)
3. Si vous vous inscrivez avec "TestUser5" comme email, le token contiendra "TestUser5"
4. Le système cherche ensuite un utilisateur avec l'email "TestUser5", qui n'existe probablement pas (ou qui a été créé par erreur)

---

## ✅ Checklist

- [ ] J'ai vérifié mon token sur jwt.io
- [ ] J'ai vérifié les utilisateurs en base de données
- [ ] J'ai créé un nouveau compte avec un **email valide** (contient @)
- [ ] J'ai vérifié que le nouveau token contient un **email** dans "username"
- [ ] J'ai utilisé le **nouveau token** pour tester `/api/user/welcome`

---

## 🚀 Test Final

Après avoir créé un compte avec un email valide :

1. **Copiez le token** de la réponse
2. **Vérifiez-le sur jwt.io** - il doit contenir un email dans "username"
3. **Testez** `POST /api/user/welcome` avec ce nouveau token

**Réponse attendue :**
```json
{
  "message": "Bienvenue connecté !",
  "welcome": "Bienvenue TestUser5 !",
  "user": {
    "id": 3,
    "email": "testuser5@example.com",
    "username": "TestUser5"
  }
}
```

---

**Le problème vient du fait que vous avez utilisé "TestUser5" comme email au lieu d'un email valide. Créez un nouveau compte avec un email valide !** ✅

