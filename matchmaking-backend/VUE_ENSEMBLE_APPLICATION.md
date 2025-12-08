# 🎯 Vue d'Ensemble de l'Application - Matchmaking avec Gamification

## 📱 Type d'Application

**Application de Matchmaking Social avec Système de Gamification**

Une plateforme où les utilisateurs peuvent :
- Se rencontrer et matcher
- Discuter via une messagerie
- Gagner des points d'expérience (XP)
- Débloquer des badges
- Compléter des quêtes
- Grimper dans le classement (leaderboard)

---

## 🎮 Fonctionnalités Principales

### 1. 🔐 Authentification
```
Inscription → Token JWT
Connexion → Token JWT
```
- Sécurisé avec JWT
- Token contient l'email de l'utilisateur

### 2. 👤 Profil Utilisateur
```
Créer/Modifier profil
Upload avatar
Gérer tags et préférences
```
- Informations complètes (bio, age, gender, location)
- Coordonnées GPS pour recommandations
- Tags personnalisés
- Préférences de recherche

### 3. 💕 Matchmaking
```
Like → Si réciproque → Match
Superlike → +10 XP → Si réciproque → Match
Dislike → Pas de match
Wink → Notification
```
- 4 types d'interactions
- Match automatique si réciproque
- +20 XP par match créé

### 4. 💬 Messagerie
```
Match → Conversation
Messages → +5 XP par message
```
- Messages dans les matches
- Historique complet
- Notifications de nouveaux messages

### 5. 🎮 Gamification
```
XP → Niveau (100 XP = 1 niveau)
Badges → Débloqués automatiquement
Quêtes → Progression et récompenses
Leaderboard → Classement par score
```

**Gains d'XP :**
- Match : +20 XP
- Message : +5 XP
- Superlike : +10 XP
- Badge : Variable
- Quête : Variable

**Badges automatiques :**
- Niveau 5, 10, 25

### 6. 🔔 Notifications
```
Match créé → Notification
Message reçu → Notification
Like reçu → Notification
```
- Notifications en temps réel
- Marquer comme lues
- Compteur de non lues

### 7. 🛡️ Modération
```
Signaler un utilisateur
Admin examine les signalements
Ban/Déban utilisateurs
```
- Système de signalement
- Panel admin pour modération
- Bannissement temporaire/permanent

### 8. 🎯 Recommandations
```
Algorithme intelligent basé sur :
- Préférences (âge, genre, distance)
- Tags en commun
- Proximité géographique
- Score de compatibilité
```
- Recommandations personnalisées
- Exclut les utilisateurs déjà matchés

---

## 🏗️ Architecture Technique

### Backend
- **Framework** : Symfony 7.3
- **PHP** : 8.2+
- **Base de données** : MySQL/PostgreSQL (via Doctrine ORM)
- **Authentification** : JWT (LexikJWTBundle)
- **API** : REST JSON

### Structure
```
Controller → Service → Repository → Entity
         ↓
    EventSubscriber
```

### Flux de Données

**Exemple : Création d'un Match**
```
1. User A like User B
   ↓
2. MatchService.createMatchRequest()
   ↓
3. Vérifie si User B a déjà liké User A
   ↓
4. Si oui → MatchService.createMatch()
   ↓
5. MatchCreatedEvent déclenché
   ↓
6. MatchCreatedSubscriber :
   - Ajoute +20 XP aux deux utilisateurs
   - Met à jour les scores
   - Envoie des notifications
```

---

## 📊 Modèles de Données Principaux

### User (Utilisateur)
```
- Informations personnelles
- Statistiques (level, xp, score)
- Relations (matches, badges, quêtes)
```

### MatchRequest (Demande de Match)
```
- fromUser → toUser
- type (like, superlike, wink, dislike)
- status (pending, matched)
```

### UserMatch (Match)
```
- userA ↔ userB
- Messages
- Dernière interaction
```

### Badge & Quest
```
- Badges : Récompenses débloquées
- Quêtes : Objectifs avec progression
```

---

## 🔄 Flux Utilisateur Typique

### 1. Inscription/Connexion
```
POST /api/register ou /api/login
→ Token JWT
```

### 2. Compléter le Profil
```
PUT /api/me
→ Bio, age, gender, location, tags, préférences
```

### 3. Upload Avatar
```
POST /api/me/avatar
→ Image uploadée
```

### 4. Voir les Recommandations
```
GET /api/recommendations
→ Liste d'utilisateurs compatibles
```

### 5. Interagir
```
POST /api/users/{id}/like
→ MatchRequest créée
→ Si réciproque → Match créé
→ +20 XP, Notification
```

### 6. Discuter
```
POST /api/matches/{id}/message
→ Message envoyé
→ +5 XP
```

### 7. Voir ses Stats
```
GET /api/me/gamification
→ Level, XP, Badges, Quêtes
```

### 8. Voir le Classement
```
GET /api/leaderboard
→ Top utilisateurs
```

---

## 🎯 Points Forts de l'Application

### ✅ Gamification Complète
- Système d'XP et de niveaux
- Badges débloquables
- Quêtes avec progression
- Leaderboard compétitif

### ✅ Matchmaking Intelligent
- Algorithme de recommandation
- 4 types d'interactions
- Match automatique si réciproque

### ✅ Engagement Utilisateur
- Notifications en temps réel
- Récompenses pour chaque action
- Progression visible

### ✅ Sécurité
- JWT sécurisé
- Validation des données
- Modération intégrée

### ✅ Scalabilité
- Architecture modulaire
- Services séparés
- Événements pour découplage

---

## 📈 Métriques de Gamification

### Calcul du Score (Leaderboard)
```
Score = XP + (nombre de matches × 10)
```

### Calcul du Niveau
```
Niveau = floor(XP / 100) + 1
```

### Gains d'XP par Action
- Match créé : **20 XP**
- Message envoyé : **5 XP**
- Superlike : **10 XP**
- Badge obtenu : **Variable**
- Quête complétée : **Variable**

---

## 🎨 Exemple de Parcours Utilisateur

### Jour 1 : Inscription
1. S'inscrire → Token JWT
2. Compléter le profil → Tags, préférences
3. Upload avatar
4. **Résultat** : Profil complet, Level 1, 0 XP

### Semaine 1 : Découverte
1. Voir les recommandations
2. Liker 10 utilisateurs
3. 2 matches créés → +40 XP
4. Envoyer 5 messages → +25 XP
5. **Résultat** : Level 1, 65 XP, 2 matches

### Semaine 2 : Engagement
1. Plus de matches → +60 XP
2. Plus de messages → +50 XP
3. Atteindre 100 XP → **Level 2**
4. **Résultat** : Level 2, Badge "Level 5" en vue

### Mois 1 : Progression
1. 25 matches → +500 XP
2. 100 messages → +500 XP
3. Atteindre 500 XP → **Level 6**
4. **Résultat** : Top 50 du leaderboard

---

## 🚀 Prêt pour la Production

✅ **Code complet et fonctionnel**  
✅ **Sécurité implémentée**  
✅ **Gamification opérationnelle**  
✅ **API REST complète**  
✅ **Documentation fournie**  

**L'application est prête à être déployée !** 🎉

