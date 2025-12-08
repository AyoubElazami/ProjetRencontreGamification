# 🔧 Solution pour "Invalid credentials" sur `/api/me`

## Problème

Vous recevez :
```json
{
    "code": 401,
    "message": "Invalid credentials."
}
```

alors que `/api/login` et `/api/register` fonctionnent.

## ✅ Solution : Utiliser le custom authenticator

Le guard JWT intégré (`jwt: ~`) peut avoir des problèmes. Utilisons notre custom authenticator qui fonctionne mieux.

### Étape 1 : Modifier `security.yaml`

Remplacez la ligne 28 dans `config/packages/security.yaml` :

**AVANT :**
```yaml
jwt: ~
```

**APRÈS :**
```yaml
custom_authenticator: App\Security\JWTAuthenticator
```

### Étape 2 : Vider le cache

```bash
php bin/console cache:clear
```

### Étape 3 : Tester

1. **POST** `http://localhost:8000/api/login`
   ```json
   {
     "email": "votre_email@example.com",
     "password": "votre_password"
   }
   ```
   → Copiez le `token`

2. **GET** `http://localhost:8000/api/me`
   - Onglet **Authorization**
   - Type : **Bearer Token**
   - Token : (collez votre token)

---

## 🔍 Si ça ne fonctionne toujours pas

### Vérification 1 : Le token est-il valide ?

Allez sur **https://jwt.io** et collez votre token.

**Le payload doit contenir :**
```json
{
  "username": "votre_email@example.com",
  "iat": ...,
  "exp": ...
}
```

### Vérification 2 : L'utilisateur existe-t-il en base ?

```bash
php bin/console doctrine:query:sql "SELECT id, email FROM user"
```

Assurez-vous que l'email dans le token correspond à un utilisateur existant.

### Vérification 3 : Message d'erreur détaillé

Avec le custom authenticator, vous devriez recevoir des messages d'erreur plus détaillés :

- `"User not found: email@example.com"` → L'utilisateur n'existe pas
- `"Invalid token: no username in payload"` → Le token n'est pas valide
- `"Invalid token format"` → Le token est malformé

---

## 📝 Configuration finale

**`config/packages/security.yaml` :**
```yaml
security:
    # ... autres configs ...
    
    firewalls:
        api:
            pattern: ^/api
            stateless: true
            provider: app_user_provider
            custom_authenticator: App\Security\JWTAuthenticator  # ← ICI
```

---

Testez et dites-moi le message d'erreur exact que vous recevez ! 🔍

