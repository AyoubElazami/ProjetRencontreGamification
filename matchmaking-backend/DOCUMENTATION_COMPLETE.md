# 📚 Documentation Complète - Application de Matchmaking avec Gamification

## 🎯 Vue d'Ensemble

Cette application est une **plateforme de matchmaking avec système de gamification** développée en **Symfony 7.3** avec **PHP 8.2+**. Elle permet aux utilisateurs de se rencontrer, de matcher, de discuter, tout en gagnant des points d'expérience (XP), des badges, et en complétant des quêtes.

---

## 🏗️ Architecture de l'Application

### Structure des Dossiers

```
src/
├── Controller/          # Contrôleurs API (endpoints REST)
│   ├── Auth/           # Authentification (Login, Register)
│   ├── User/           # Gestion des profils utilisateurs
│   ├── Matchmaking/    # Matchmaking et interactions
│   ├── Gamification/   # Système de gamification
│   ├── Notification/   # Notifications
│   ├── Moderation/     # Signalements
│   └── Admin/          # Administration
├── Entity/             # Entités Doctrine (modèles de données)
├── Service/            # Services métier (logique)
├── Repository/         # Repositories Doctrine
├── Security/           # Authentification JWT
├── EventSubscriber/    # Écouteurs d'événements
└── Event/              # Événements personnalisés
```

---

## 🔐 1. AUTHENTIFICATION

### Endpoints

#### `POST /api/register` - Inscription
- Crée un nouveau compte utilisateur
- Retourne un token JWT immédiatement
- Valide l'email et le mot de passe

#### `POST /api/login` - Connexion
- Authentifie un utilisateur existant
- Retourne un token JWT

**Sécurité :**
- JWT (JSON Web Tokens) avec LexikJWTBundle
- Hashage des mots de passe avec Symfony PasswordHasher
- Token contient l'email de l'utilisateur (via `JWTEventSubscriber`)

---

## 👤 2. GESTION DES PROFILS UTILISATEURS

### Endpoints

#### `GET /api/me` - Récupérer mon profil
Retourne toutes les informations de l'utilisateur connecté :
- Informations personnelles (email, username, bio, age, gender, location)
- Coordonnées GPS (latitude, longitude)
- Préférences de recherche
- Tags de profil
- Avatar
- Statistiques de gamification (level, XP, score)
- Badges et quêtes

#### `PUT /api/me` - Mettre à jour mon profil
Met à jour partiellement ou complètement le profil :
- `username`, `bio`, `gender`, `age`, `location`
- `latitude`, `longitude` (coordonnées GPS)
- `preferences` (JSON avec age_range, distance_km, gender_pref)
- `tags` (array de noms de tags - créés automatiquement s'ils n'existent pas)

#### `POST /api/me/avatar` - Upload d'avatar
- Upload d'une image (form-data)
- Remplace automatiquement l'ancien avatar
- Stockage dans `/public/uploads/avatars/{userId}/`

#### `POST /api/user/welcome` - Message de bienvenue
- Endpoint de test pour vérifier l'authentification
- Retourne un message personnalisé

#### `GET /api/users` - Liste des utilisateurs
- Pagination (page, limit)
- Exclut l'utilisateur connecté

#### `GET /api/users/{id}` - Détails d'un utilisateur
- Informations publiques d'un utilisateur

#### `GET /api/recommendations` - Recommandations
- Algorithme de recommandation basé sur :
  - Préférences (âge, genre, distance)
  - Tags en commun
  - Proximité géographique
  - Score de compatibilité

---

## 💕 3. MATCHMAKING

### Types d'Interactions

1. **Like** (`POST /api/users/{id}/like`)
   - J'aime un utilisateur
   - Si réciproque → Match créé automatiquement

2. **Dislike** (`POST /api/users/{id}/dislike`)
   - Je n'aime pas un utilisateur
   - Pas de match possible

3. **Superlike** (`POST /api/users/{id}/superlike`)
   - J'aime beaucoup un utilisateur
   - +10 XP
   - Si réciproque → Match créé

4. **Wink** (`POST /api/users/{id}/wink`)
   - Clin d'œil à un utilisateur
   - Notification envoyée

### Endpoints de Match

#### `GET /api/matches` - Liste de mes matches
- Tous les matches de l'utilisateur connecté
- Avec informations de l'autre utilisateur

#### `GET /api/matches/{id}` - Détails d'un match
- Informations complètes d'un match spécifique

#### `GET /api/matches/{id}/messages` - Messages d'un match
- Pagination (limit, offset)
- Historique des messages

#### `POST /api/matches/{id}/message` - Envoyer un message
- Envoie un message dans un match
- +5 XP par message
- Met à jour `lastInteractionAt`

### Logique de Match

1. **Création d'une MatchRequest** :
   - Utilisateur A like/utilisateur B
   - Une `MatchRequest` est créée avec status `PENDING`

2. **Match réciproque** :
   - Si utilisateur B a déjà liké/utilisateur A
   - Un `UserMatch` est créé automatiquement
   - Les deux `MatchRequest` passent à `MATCHED`
   - Événement `MatchCreatedEvent` déclenché

3. **Événement MatchCreated** :
   - +20 XP pour chaque utilisateur
   - Notification envoyée aux deux utilisateurs
   - Score du leaderboard mis à jour

---

## 🎮 4. GAMIFICATION

### Système de Points d'Expérience (XP)

**Gains d'XP :**
- **Match créé** : +20 XP
- **Message envoyé** : +5 XP
- **Superlike** : +10 XP
- **Badge obtenu** : Variable (défini dans le badge)
- **Quête complétée** : Variable (défini dans la quête)

**Calcul du Niveau :**
```
Niveau = floor(XP / 100) + 1
```
- Chaque niveau nécessite 100 XP
- Niveau minimum : 1

### Badges

**Badges automatiques :**
- `level_5` : Atteindre le niveau 5
- `level_10` : Atteindre le niveau 10
- `level_25` : Atteindre le niveau 25

**Structure d'un Badge :**
- `code` : Identifiant unique (ex: "level_5")
- `name` : Nom du badge
- `description` : Description
- `icon` : URL de l'icône
- `xpReward` : XP gagné lors de l'obtention

### Quêtes

**Structure d'une Quête :**
- `code` : Identifiant unique
- `title` : Titre de la quête
- `description` : Description
- `xpReward` : XP gagné à la complétion
- `conditions` : JSON avec les conditions (ex: `{"matches": 5, "messages": 10}`)

**Progression :**
- Chaque utilisateur a une `UserQuest` avec un `progress` (JSON)
- Quand les conditions sont remplies → Quête complétée
- XP ajouté automatiquement

### Leaderboard

**Calcul du Score :**
```
Score = XP + (nombre de matches × 10)
```

**Endpoint :**
- `GET /api/leaderboard` : Top 100 utilisateurs (par défaut 10)
- Trié par : Score DESC, Level DESC, XP DESC

### Endpoints Gamification

#### `GET /api/me/gamification` - Mes stats de gamification
Retourne :
- Level, XP, Score
- Liste des badges obtenus
- Liste des quêtes (avec progression)

#### `GET /api/leaderboard` - Classement
- Top utilisateurs avec pagination
- Rank, user info, level, XP, score

---

## 🔔 5. NOTIFICATIONS

### Types de Notifications

- `TYPE_MATCH` : Nouveau match créé
- `TYPE_MESSAGE` : Nouveau message reçu
- `TYPE_LIKE` : Quelqu'un vous a liké
- `TYPE_SUPERLIKE` : Quelqu'un vous a superliké
- `TYPE_WINK` : Quelqu'un vous a fait un clin d'œil

### Endpoints

#### `GET /api/notifications` - Liste des notifications
- Pagination (limit, offset)
- Retourne aussi `unreadCount`

#### `POST /api/notifications/{id}/read` - Marquer comme lue
- Marque une notification comme lue

#### `POST /api/notifications/read-all` - Tout marquer comme lu
- Marque toutes les notifications comme lues

**Structure d'une Notification :**
- `type` : Type de notification
- `payload` : JSON avec les données (matchId, userId, username, etc.)
- `readAt` : Date de lecture (null si non lue)
- `createdAt` : Date de création

---

## 🛡️ 6. MODÉRATION

### Signalements

#### `POST /api/reports` - Signaler un utilisateur
- `targetUserId` : ID de l'utilisateur signalé
- `reason` : Raison du signalement
- `details` : Détails supplémentaires (optionnel)

**Raisons possibles :**
- Harassment
- Inappropriate content
- Spam
- Fake profile
- Other

### Administration (ROLE_ADMIN requis)

#### `GET /api/admin/reports` - Liste des signalements
- Tous les signalements en attente

#### `GET /api/admin/reports/{id}` - Détails d'un signalement
- Informations complètes

#### `POST /api/admin/reports/{id}/review` - Examiner un signalement
- `status` : `reviewed`, `action_taken`, `dismissed`
- `notes` : Notes de l'admin (optionnel)

#### `POST /api/admin/users/{id}/ban` - Bannir un utilisateur
- `until` : Date de fin du ban (optionnel, permanent si null)

#### `POST /api/admin/users/{id}/unban` - Débannir un utilisateur
- Retire le ban d'un utilisateur

---

## 📊 7. ENTITÉS (Modèles de Données)

### User (Utilisateur)
- Informations personnelles (email, username, bio, age, gender, location)
- Coordonnées GPS (latitude, longitude)
- Préférences de recherche (JSON)
- Tags de profil (ManyToMany avec ProfileTag)
- Statistiques (level, xp, score)
- Avatar URL
- Relations : MatchRequests, Matches, Messages, Badges, Quêtes, Notifications

### MatchRequest (Demande de Match)
- `fromUser` : Utilisateur qui envoie
- `toUser` : Utilisateur qui reçoit
- `type` : like, dislike, superlike, wink
- `status` : pending, matched, unmatched

### UserMatch (Match)
- `userA` et `userB` : Les deux utilisateurs qui matchent
- `createdAt` : Date de création
- `lastInteractionAt` : Dernière interaction
- Messages (OneToMany)

### Message
- `userMatch` : Le match dans lequel le message est envoyé
- `sender` : L'utilisateur qui envoie
- `content` : Contenu du message
- `readAt` : Date de lecture
- `createdAt` : Date d'envoi

### Badge
- `code` : Identifiant unique
- `name`, `description`, `icon`
- `xpReward` : XP gagné

### UserBadge
- Relation User ↔ Badge
- `awardedAt` : Date d'obtention

### Quest
- `code` : Identifiant unique
- `title`, `description`
- `xpReward` : XP gagné
- `conditions` : JSON avec les conditions

### UserQuest
- Relation User ↔ Quest
- `progress` : JSON avec la progression
- `completedAt` : Date de complétion

### Notification
- `user` : Utilisateur destinataire
- `type` : Type de notification
- `payload` : JSON avec les données
- `readAt` : Date de lecture

### Report (Signalement)
- `reporter` : Utilisateur qui signale
- `targetUser` : Utilisateur signalé
- `reason` : Raison
- `details` : Détails
- `status` : pending, reviewed, action_taken, dismissed

### ProfileTag
- `name` : Nom du tag
- Utilisateurs (ManyToMany)

---

## 🔧 8. SERVICES MÉTIER

### GamificationService
- `addXp()` : Ajouter XP à un utilisateur
- `calculateLevel()` : Calculer le niveau à partir de l'XP
- `awardBadge()` : Attribuer un badge
- `updateQuestProgress()` : Mettre à jour la progression d'une quête
- `updateLeaderboardScore()` : Mettre à jour le score du leaderboard
- `checkLevelUpBadges()` : Vérifier et attribuer les badges de niveau

### MatchService
- `createMatchRequest()` : Créer une demande de match
- `createMatch()` : Créer un match (si réciproque)
- `getUserMatches()` : Récupérer les matches d'un utilisateur

### RecommendationService
- `getRecommendations()` : Algorithme de recommandation
- `calculateCompatibilityScore()` : Calculer le score de compatibilité
- `calculateDistance()` : Calculer la distance entre deux points GPS

### NotificationService
- `createNotification()` : Créer une notification
- `getUserNotifications()` : Récupérer les notifications d'un utilisateur
- `markAsRead()` : Marquer comme lue
- `markAllAsRead()` : Tout marquer comme lu
- `getUnreadCount()` : Nombre de notifications non lues

### ModerationService
- `createReport()` : Créer un signalement
- `getPendingReports()` : Récupérer les signalements en attente
- `reviewReport()` : Examiner un signalement
- `banUser()` : Bannir un utilisateur
- `unbanUser()` : Débannir un utilisateur

### MediaService
- `uploadAvatar()` : Upload d'un avatar
- `deleteAvatar()` : Supprimer un avatar

---

## 🎯 9. ÉVÉNEMENTS ET ÉCOUTEURS

### MatchCreatedEvent
Déclenché quand un match est créé.

### MatchCreatedSubscriber
Écoute `MatchCreatedEvent` et :
- Ajoute +20 XP aux deux utilisateurs
- Met à jour les scores du leaderboard
- Envoie des notifications aux deux utilisateurs

### JWTEventSubscriber
Écoute `lexik_jwt_authentication.on_jwt_created` et :
- Force l'utilisation de `getUserIdentifier()` (email) dans le token
- Corrige le problème où LexikJWTBundle utilisait `getUsername()` au lieu de l'email

---

## 🔒 10. SÉCURITÉ

### Authentification
- **JWT (JSON Web Tokens)** avec LexikJWTBundle
- Token dans le header : `Authorization: Bearer {token}`
- Token expire après un certain temps (configuré dans LexikJWTBundle)

### Autorisation
- **ROLE_USER** : Utilisateur normal (accès à toutes les fonctionnalités)
- **ROLE_ADMIN** : Administrateur (accès aux endpoints admin)

### Protection des Routes
- Toutes les routes (sauf `/api/login` et `/api/register`) nécessitent `ROLE_USER`
- Routes admin nécessitent `ROLE_ADMIN`

### Validation
- Validation des données avec Symfony Validator
- Contraintes sur les entités (email, age, etc.)

---

## 📈 11. FONCTIONNALITÉS DE GAMIFICATION DÉTAILLÉES

### Système de Niveaux
- **Niveau 1** : 0-99 XP
- **Niveau 2** : 100-199 XP
- **Niveau 3** : 200-299 XP
- etc.

### Badges Disponibles
1. **Level 5** : Atteindre le niveau 5
2. **Level 10** : Atteindre le niveau 10
3. **Level 25** : Atteindre le niveau 25

### Quêtes (Exemples)
Les quêtes peuvent être configurées avec des conditions comme :
- `{"matches": 5}` : Faire 5 matches
- `{"messages": 10}` : Envoyer 10 messages
- `{"superlikes": 3}` : Faire 3 superlikes

### Leaderboard
- Score basé sur XP + (matches × 10)
- Classement en temps réel
- Top 100 par défaut

---

## 🚀 12. ENDPOINTS COMPLETS

### Authentification
- `POST /api/register` - Inscription
- `POST /api/login` - Connexion

### Profil Utilisateur
- `GET /api/me` - Mon profil
- `PUT /api/me` - Mettre à jour mon profil
- `POST /api/me/avatar` - Upload avatar
- `POST /api/user/welcome` - Message de bienvenue

### Utilisateurs
- `GET /api/users` - Liste des utilisateurs
- `GET /api/users/{id}` - Détails d'un utilisateur
- `GET /api/recommendations` - Recommandations

### Matchmaking
- `POST /api/users/{id}/like` - Like
- `POST /api/users/{id}/dislike` - Dislike
- `POST /api/users/{id}/superlike` - Superlike
- `POST /api/users/{id}/wink` - Wink
- `GET /api/matches` - Mes matches
- `GET /api/matches/{id}` - Détails d'un match
- `GET /api/matches/{id}/messages` - Messages d'un match
- `POST /api/matches/{id}/message` - Envoyer un message

### Gamification
- `GET /api/me/gamification` - Mes stats
- `GET /api/leaderboard` - Classement

### Notifications
- `GET /api/notifications` - Liste des notifications
- `POST /api/notifications/{id}/read` - Marquer comme lue
- `POST /api/notifications/read-all` - Tout marquer comme lu

### Modération
- `POST /api/reports` - Signaler un utilisateur

### Administration (ROLE_ADMIN)
- `GET /api/admin/reports` - Liste des signalements
- `GET /api/admin/reports/{id}` - Détails d'un signalement
- `POST /api/admin/reports/{id}/review` - Examiner un signalement
- `POST /api/admin/users/{id}/ban` - Bannir un utilisateur
- `POST /api/admin/users/{id}/unban` - Débannir un utilisateur

---

## ✅ 13. VÉRIFICATIONS ET CORRECTIONS

### ✅ Corrections Appliquées

1. **JWT Token** : Correction du problème où le token contenait le username au lieu de l'email
   - Fichier : `src/EventSubscriber/JWTEventSubscriber.php`
   - Force l'utilisation de `getUserIdentifier()` (email) dans le payload

### ✅ Code Vérifié

- ✅ Tous les contrôleurs sont correctement structurés
- ✅ Tous les services implémentent la logique métier
- ✅ Toutes les entités sont bien définies avec leurs relations
- ✅ Système de gamification complet et fonctionnel
- ✅ Notifications automatiques lors des matches
- ✅ Système de modération opérationnel
- ✅ Sécurité JWT correctement configurée
- ✅ Validation des données en place
- ✅ Pas d'erreurs de linting

---

## 📝 14. NOTES IMPORTANTES

### Base de Données
- Table `user` (avec backticks car mot réservé SQL)
- Migrations Doctrine pour la structure
- Relations bien définies avec cascade

### Upload de Fichiers
- Avatars stockés dans `/public/uploads/avatars/{userId}/`
- Service `MediaService` pour gérer les uploads

### Algorithme de Recommandation
- Basé sur les préférences utilisateur
- Score de compatibilité calculé avec :
  - Tags en commun
  - Proximité d'âge
  - Distance géographique
- Exclut les utilisateurs déjà matchés

### Performance
- Index sur les colonnes fréquemment utilisées
- Pagination sur les listes
- Requêtes optimisées avec QueryBuilder

---

## 🎉 CONCLUSION

L'application est **complète et fonctionnelle** avec :

✅ Authentification JWT sécurisée  
✅ Gestion complète des profils utilisateurs  
✅ Système de matchmaking avec interactions (like, superlike, wink)  
✅ Messagerie entre matches  
✅ **Système de gamification complet** (XP, niveaux, badges, quêtes, leaderboard)  
✅ Notifications automatiques  
✅ Système de modération et administration  
✅ Algorithme de recommandation intelligent  
✅ Upload d'avatars  
✅ Géolocalisation pour les recommandations  

**Tout est prêt pour la production !** 🚀

