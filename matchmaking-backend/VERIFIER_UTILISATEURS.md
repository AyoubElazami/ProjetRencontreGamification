# 📋 Vérifier les utilisateurs dans la base de données

## Table : `user` (avec backticks car c'est un mot réservé SQL)

## Problème actuel

Votre token contient `"TestUser"` au lieu d'un **email**. 

LexikJWTBundle stocke `getUserIdentifier()` dans le champ `"username"` du token JWT. Comme `getUserIdentifier()` retourne l'email, le token devrait contenir votre email, pas "TestUser".

## Vérifications

### 1. Vérifier les utilisateurs en base

```bash
php bin/console doctrine:query:sql "SELECT id, email, username FROM \`user\`"
```

### 2. Vérifier le contenu de votre token

Allez sur **https://jwt.io** et collez votre token.

**Le payload devrait contenir :**
```json
{
  "username": "votre_email@example.com",  ← Doit être un EMAIL
  "iat": ...,
  "exp": ...
}
```

**Si vous voyez :**
```json
{
  "username": "TestUser",  ← ❌ PROBLÈME
  ...
}
```

Cela signifie que :
- Soit vous vous êtes inscrit avec l'email "TestUser" (invalide)
- Soit le token a été créé incorrectement

### 3. Solution

**A. Recréer un compte avec un email valide :**

```json
POST /api/register
{
  "email": "test@example.com",  ← Email VALIDE
  "password": "password123",
  "username": "TestUser"  ← Le username peut être "TestUser"
}
```

**B. Ou vous connecter avec un compte existant :**

```json
POST /api/login
{
  "email": "votre_email@example.com",  ← Email qui existe en base
  "password": "votre_password"
}
```

---

## Table : `user`

Les utilisateurs sont stockés dans la table **`user`** (avec backticks).

**Colonnes principales :**
- `id` : ID de l'utilisateur
- `email` : Email (UNIQUE) - utilisé pour l'authentification
- `username` : Nom d'utilisateur (nullable)
- `password` : Mot de passe hashé
- `roles` : JSON avec les rôles
- etc.

---

## Commande pour voir tous les utilisateurs

```bash
php bin/console doctrine:query:sql "SELECT id, email, username FROM \`user\`"
```

Ou pour voir tous les détails :
```bash
php bin/console doctrine:query:sql "SELECT * FROM \`user\`"
```

