# 🔍 Débogage de l'erreur "Bad credentials"

## Problème

Vous recevez `{"error": "Bad credentials."}` lors de l'authentification avec un token JWT.

## Solution rapide

### Vérification 1 : Le token est-il valide ?

Décodez votre token JWT pour voir son contenu :

1. Allez sur https://jwt.io
2. Collez votre token dans la section "Encoded"
3. Vérifiez que le payload contient bien `"username"` avec l'email

**Exemple de payload attendu :**
```json
{
  "username": "test@example.com",
  "iat": 1234567890,
  "exp": 1234571490
}
```

### Vérification 2 : L'utilisateur existe-t-il ?

Vérifiez que l'utilisateur existe bien dans la base de données avec l'email correspondant.

### Solution : Utiliser le guard JWT intégré

Plutôt que d'utiliser un custom authenticator, utilisons le guard JWT intégré de LexikJWTBundle qui gère tout automatiquement.

