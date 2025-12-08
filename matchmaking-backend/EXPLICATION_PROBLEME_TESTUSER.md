# 🔍 Explication du problème "User not found: TestUser"

## Le problème

Votre token JWT contient :
```json
{
  "username": "TestUser"  ← ❌ PROBLÈME
}
```

Mais le système cherche un utilisateur avec l'**email** "TestUser", qui n'existe probablement pas.

## Pourquoi ?

1. **LexikJWTBundle** stocke `getUserIdentifier()` dans le champ `"username"` du token
2. **`getUserIdentifier()`** retourne l'**email** de l'utilisateur (voir ligne 138 de `User.php`)
3. Donc le token devrait contenir votre **email**, pas "TestUser"

## La table : `user`

Les utilisateurs sont stockés dans la table **`user`** (avec backticks car c'est un mot réservé SQL).

**Structure :**
- `id` : ID unique
- `email` : Email (UNIQUE) ← utilisé pour l'authentification
- `username` : Nom d'utilisateur (peut être NULL)
- `password` : Mot de passe hashé
- etc.

## Solution

### 1. Vérifier vos utilisateurs

Exécutez cette commande dans votre terminal :
```bash
php bin/console doctrine:query:sql "SELECT id, email, username FROM \`user\`"
```

### 2. Se reconnecter avec un email valide

**Option A : S'inscrire avec un email valide**
```json
POST /api/register
{
  "email": "test@example.com",  ← Email VALIDE (pas "TestUser")
  "password": "password123",
  "username": "TestUser"  ← Le username peut être "TestUser"
}
```

**Option B : Se connecter avec un compte existant**
```json
POST /api/login
{
  "email": "votre_email_valide@example.com",  ← Email qui existe en base
  "password": "votre_password"
}
```

### 3. Vérifier le token

Après le login/register, allez sur **https://jwt.io** et collez votre token.

**Le payload devrait contenir :**
```json
{
  "username": "test@example.com",  ← Doit être un EMAIL
  "iat": ...,
  "exp": ...
}
```

---

## Résumé

- **Table** : `user` (avec backticks)
- **Problème** : Le token contient "TestUser" au lieu d'un email
- **Solution** : Utiliser un email valide lors du login/register

