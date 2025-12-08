# 🔍 Explication de l'Erreur et de la Solution

## ❌ L'ERREUR QUE VOUS AVIEZ

### Le Problème

Quand vous vous connectiez avec :
```json
{
  "email": "test5@example.com",
  "password": "password123"
}
```

Vous receviez cette erreur :
```json
{
  "code": 401,
  "message": "User not found: TestUser5"
}
```

### Pourquoi cette erreur ?

1. **LexikJWTBundle** créait le token JWT en utilisant `getUsername()` au lieu de `getUserIdentifier()`
2. Dans votre entité `User` :
   - `getUsername()` retourne `"TestUser5"` (le nom d'utilisateur)
   - `getUserIdentifier()` retourne `"test5@example.com"` (l'email)
3. Le token contenait donc `"username": "TestUser5"` au lieu de `"username": "test5@example.com"`
4. Quand vous utilisiez ce token, le système cherchait un utilisateur avec l'**email** "TestUser5", qui n'existe pas
5. D'où l'erreur : `"User not found: TestUser5"`

### Exemple du Token (AVANT la correction)

Si vous décodiez le token sur jwt.io, vous voyiez :
```json
{
  "iat": 1765232519,
  "exp": 1765236119,
  "roles": ["ROLE_USER"],
  "username": "TestUser5"  ← ❌ PROBLÈME : C'est le username, pas l'email !
}
```

Le système cherchait alors un utilisateur avec l'email "TestUser5", qui n'existe pas.

---

## ✅ LA SOLUTION

### Changement Effectué

**Fichier créé :** `src/EventSubscriber/JWTEventSubscriber.php`

Ce fichier intercepte la création du token JWT et force l'utilisation de `getUserIdentifier()` (email) au lieu de `getUsername()`.

### Comment ça fonctionne ?

1. Quand LexikJWTBundle crée un token, il déclenche l'événement `lexik_jwt_authentication.on_jwt_created`
2. Notre `JWTEventSubscriber` intercepte cet événement
3. Il remplace le champ `username` dans le payload par `getUserIdentifier()` (l'email)
4. Le token contient maintenant l'email au lieu du username

### Code de la Solution

```php
<?php

namespace App\EventSubscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onJWTCreated',
        ];
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();
        
        // S'assurer que le token contient getUserIdentifier() (email) et non getUsername()
        $payload = $event->getData();
        
        // Remplacer 'username' par getUserIdentifier() si l'utilisateur a cette méthode
        if (method_exists($user, 'getUserIdentifier')) {
            $payload['username'] = $user->getUserIdentifier(); // ← Force l'email
        }
        
        $event->setData($payload);
    }
}
```

### Exemple du Token (APRÈS la correction)

Maintenant, si vous décodez le token sur jwt.io, vous voyez :
```json
{
  "iat": 1765232519,
  "exp": 1765236119,
  "roles": ["ROLE_USER"],
  "username": "test5@example.com"  ← ✅ CORRECT : C'est l'email !
}
```

Le système cherche maintenant un utilisateur avec l'email "test5@example.com", qui existe bien !

---

## 📋 RÉSUMÉ

### L'Erreur
- **Message :** `"User not found: TestUser5"`
- **Cause :** Le token JWT contenait le username ("TestUser5") au lieu de l'email ("test5@example.com")
- **Raison :** LexikJWTBundle utilisait `getUsername()` au lieu de `getUserIdentifier()`

### La Solution
- **Fichier créé :** `src/EventSubscriber/JWTEventSubscriber.php`
- **Fonction :** Intercepte la création du token et force l'utilisation de `getUserIdentifier()` (email)
- **Résultat :** Le token contient maintenant l'email, et l'authentification fonctionne

### Fichiers Modifiés
- ✅ **NOUVEAU :** `src/EventSubscriber/JWTEventSubscriber.php` (créé)
- ❌ **AUCUN autre fichier modifié**

---

## 🧪 Vérification

Pour vérifier que ça fonctionne :

1. **Connectez-vous :**
   ```bash
   POST http://localhost:8000/api/login
   Body: {"email": "test5@example.com", "password": "password123"}
   ```

2. **Copiez le token** de la réponse

3. **Décodez-le sur jwt.io** - Le payload doit contenir :
   ```json
   {
     "username": "test5@example.com"  ← Doit être l'email
   }
   ```

4. **Testez un endpoint protégé :**
   ```bash
   POST http://localhost:8000/api/user/welcome
   Headers: Authorization: Bearer {votre_token}
   ```

5. **Résultat attendu :** ✅ Succès au lieu de l'erreur 401

---

**Le problème est maintenant résolu ! 🎉**

