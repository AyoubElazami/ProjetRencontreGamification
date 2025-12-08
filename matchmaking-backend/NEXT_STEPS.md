# Prochaines étapes - Backend Matchmaking

## 1. Générer les migrations Doctrine

Après avoir créé toutes les entités, vous devez générer les migrations pour créer les tables dans la base de données :

```bash
cd matchmaking-backend
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

Cela va :
- Créer un fichier de migration dans `migrations/`
- Appliquer la migration pour créer toutes les tables

## 2. Créer le dossier d'upload pour les avatars

```bash
mkdir -p public/uploads/avatars
chmod 777 public/uploads/avatars
```

## 3. Configuration de l'environnement

Assurez-vous que votre fichier `.env` contient :

```env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/matchmaking?serverVersion=8.0&charset=utf8mb4"
APP_SECRET="votre_secret_ici"
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE="votre_passphrase_jwt"
```

## 4. Générer les clés JWT (si pas déjà fait)

```bash
php bin/console lexik:jwt:generate-keypair
```

## 5. Créer des données de base (Badges, Quêtes)

Vous pouvez créer une commande Symfony ou des fixtures pour initialiser :
- Des badges de base (level_5, level_10, level_25, etc.)
- Des quêtes de base

Exemple de commande à créer :

```php
// src/Command/LoadFixturesCommand.php
php bin/console app:load-fixtures
```

## 6. Tester l'API

Vous pouvez utiliser Postman, cURL, ou créer un client de test :

### Test d'inscription :
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"test123","username":"testuser"}'
```

### Test de login :
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"test123"}'
```

### Test d'accès protégé :
```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer {votre_token_jwt}"
```

## 7. Améliorations futures

### Rate Limiting
Implémenter le rate limiting pour éviter le spam :
- Utiliser Symfony RateLimiter component
- Limiter les likes/messages par minute/heure

### WebSockets / Server-Sent Events
Pour les notifications en temps réel :
- Utiliser Mercure ou WebSockets
- Notifier les utilisateurs quand ils reçoivent un like/match/message

### Push Notifications
Pour les notifications mobiles :
- Intégrer Firebase Cloud Messaging (FCM)
- Intégrer Apple Push Notification Service (APNs)

### Recherche avancée
Améliorer le système de recommandations :
- Filtres supplémentaires (tags, distance, etc.)
- Algorithmes de scoring plus sophistiqués

### Tests
Créer des tests unitaires et fonctionnels :
```bash
php bin/phpunit
```

### Cache
Mettre en cache les recommandations et le leaderboard :
- Utiliser Redis ou Symfony Cache
- Invalider le cache lors des updates

## 8. Sécurité supplémentaire

### Voters
Créer des Voters Symfony pour :
- Vérifier qu'un utilisateur peut modifier son propre profil
- Vérifier qu'un utilisateur peut accéder à ses propres matches

Exemple :
```php
// src/Security/Voter/UserVoter.php
```

### Validation
Ajouter plus de validations :
- Validation des formats d'image pour les avatars
- Validation des coordonnées GPS
- Sanitization du contenu des messages

### Rate Limiting par route
```yaml
# config/packages/rate_limiter.yaml
```

## 9. Monitoring et Logging

- Configurer Monolog pour logger les actions importantes
- Ajouter des métriques (nombre de matches, messages, etc.)
- Monitoring des performances

## 10. Documentation API

- Générer la documentation avec NelmioApiDocBundle ou API Platform
- Créer une collection Postman pour tester l'API

## Structure finale

```
matchmaking-backend/
├── src/
│   ├── Controller/
│   │   ├── Auth/          (Login, Register)
│   │   ├── User/          (User, Profile)
│   │   ├── Matchmaking/   (Match, Interaction)
│   │   ├── Gamification/  (XP, Leaderboard)
│   │   ├── Notification/  (Notifications)
│   │   ├── Admin/         (Admin panel)
│   │   └── Moderation/    (Reports)
│   ├── Entity/            (Toutes les entités)
│   ├── Repository/        (Repositories)
│   ├── Service/           (Services métier)
│   ├── Event/             (Events)
│   ├── EventSubscriber/   (Subscribers)
│   ├── EventListener/     (Listeners)
│   └── Security/          (JWTAuthenticator, Voters)
├── config/
├── migrations/
└── public/
```

## Commandes utiles

```bash
# Vider le cache
php bin/console cache:clear

# Créer une nouvelle migration
php bin/console doctrine:migrations:diff

# Appliquer les migrations
php bin/console doctrine:migrations:migrate

# Créer une nouvelle entité
php bin/console make:entity

# Créer un nouveau contrôleur
php bin/console make:controller

# Créer une nouvelle commande
php bin/console make:command
```

Bon développement ! 🚀

