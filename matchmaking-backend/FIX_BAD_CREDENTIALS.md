# 🔧 Fix "Bad credentials" - Guide de dépannage

## Problème

Vous recevez `{"error": "Bad credentials."}` lors de l'authentification avec un token JWT.

## Solutions

### Solution 1 : Utiliser le guard JWT intégré (recommandé)

J'ai configuré le guard JWT intégré de LexikJWTBundle dans `security.yaml` avec `jwt: ~`.

**Actions :**

1. **Vider le cache :**
```bash
php bin/console cache:clear
```

2. **Tester dans Postman :**

   **POST** `http://localhost:8000/api/login`
   ```json
   {
     "email": "votre_email@example.com",
     "password": "votre_password"
   }
   ```
   
   → Copiez le `token` de la réponse

   **GET** `http://localhost:8000/api/me`
   - Onglet **Authorization**
   - Type: `Bearer Token`
   - Token: (collez votre token)

### Solution 2 : Vérifier le payload du token

Pour voir ce que contient votre token :

1. Allez sur https://jwt.io
2. Collez votre token dans la section "Encoded"
3. Vérifiez le payload - il doit contenir `"username"` avec l'email

### Solution 3 : Vérifier que l'utilisateur existe

Assurez-vous que l'utilisateur existe bien dans la base de données avec l'email utilisé lors du login.

### Solution 4 : Revenir au custom authenticator

Si le guard intégré ne fonctionne pas, vous pouvez utiliser notre custom authenticator en remplaçant dans `security.yaml` :

```yaml
custom_authenticator: App\Security\JWTAuthenticator
```

au lieu de :
```yaml
jwt: ~
```

---

## Configuration actuelle

Le guard JWT intégré est configuré dans `security.yaml` :
- Il utilise automatiquement le UserProvider configuré
- Il décode et valide le token automatiquement
- Il charge l'utilisateur via `loadUserByIdentifier()`

---

Testez et dites-moi si ça fonctionne ! 🚀

