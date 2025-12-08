# 📦 Guide de Remplissage de la Base de Données

## 🚀 Commande de Fixtures

Une commande Symfony a été créée pour remplir automatiquement toutes les tables de la base de données avec des données de test.

### Utilisation

```bash
php bin/console app:load-fixtures
```

---

## 📊 Données Créées

### 1. **Badges** (7 badges)
- `level_5` - Niveau 5 (🎯, +50 XP)
- `level_10` - Niveau 10 (⭐, +100 XP)
- `level_25` - Niveau 25 (🏆, +250 XP)
- `first_match` - Premier Match (💕, +30 XP)
- `social_butterfly` - Papillon Social (💬, +75 XP)
- `superliker` - Super Liker (✨, +50 XP)
- `match_maker` - Cupidon (💘, +150 XP)

### 2. **Quêtes** (5 quêtes)
- `quest_first_match` - Créer 1 match (+50 XP)
- `quest_5_matches` - Créer 5 matches (+100 XP)
- `quest_10_messages` - Envoyer 10 messages (+75 XP)
- `quest_3_superlikes` - Faire 3 superlikes (+60 XP)
- `quest_complete_profile` - Compléter le profil (+40 XP)

### 3. **Tags de Profil** (24 tags)
- Sport, Musique, Cinéma, Voyage, Cuisine, Lecture
- Art, Gaming, Nature, Photographie, Danse, Théâtre
- Mode, Tech, Science, Histoire, Philosophie, Yoga
- Fitness, Randonnée, Plage, Montagne, Ville, Campagne

### 4. **Utilisateurs** (15 utilisateurs)
Chaque utilisateur a :
- Email unique (ex: `alice@example.com`)
- Username
- Mot de passe : `password123` (pour tous)
- Profil complet (bio, age, gender, location, coordonnées GPS)
- Tags de profil (3-4 tags par utilisateur)
- XP et niveau variés
- Préférences de recherche

**Liste des utilisateurs :**
- Alice (25 ans, F) - Paris - Voyage, Photographie, Art
- Bob (28 ans, M) - Lyon - Sport, Musique, Gaming
- Charlie (30 ans, M) - Marseille - Cinéma, Cuisine, Lecture
- Diana (27 ans, F) - Toulouse - Yoga, Nature, Fitness
- Eve (24 ans, F) - Nice - Art, Photographie, Mode
- Frank (32 ans, M) - Bordeaux - Tech, Gaming, Science
- Grace (26 ans, F) - Nantes - Danse, Théâtre, Art
- Henry (29 ans, M) - Grenoble - Randonnée, Nature, Montagne
- Iris (23 ans, F) - Cannes - Plage, Voyage, Mode
- Jack (31 ans, M) - Strasbourg - Histoire, Philosophie, Lecture
- Kate (25 ans, F) - Lille - Fitness, Sport, Yoga
- Liam (27 ans, M) - Rennes - Gaming, Tech, Science
- Mia (24 ans, F) - Montpellier - Ville, Art, Cinéma
- Noah (28 ans, M) - Dijon - Campagne, Nature, Lecture
- Olivia (26 ans, F) - Reims - Mode, Art, Voyage

### 5. **Badges Attribués**
- Les utilisateurs de niveau 5+ reçoivent le badge `level_5`
- Les utilisateurs de niveau 10+ reçoivent les badges `level_5` et `level_10`
- Les utilisateurs de niveau 25+ reçoivent tous les badges de niveau

### 6. **Quêtes Attribuées**
- Chaque utilisateur reçoit 2-4 quêtes aléatoires
- Avec progression aléatoire (0-80% de complétion)

### 7. **Matches** (~8 matches)
- Créés entre différents utilisateurs
- Avec `lastInteractionAt` défini

### 8. **Messages** (~20-30 messages)
- Messages dans les matches existants
- 2-6 messages par match
- Alternance entre les deux utilisateurs

### 9. **Notifications** (~15-60 notifications)
- Notifications de type : MATCH, LIKE, MESSAGE
- Certaines déjà lues, d'autres non lues
- 1-4 notifications par utilisateur

### 10. **Match Requests** (~15 requêtes)
- Requêtes de type : LIKE, SUPERLIKE, WINK
- Status : PENDING
- Entre différents utilisateurs

---

## 🔄 Réexécution

La commande est **idempotente** :
- Elle vérifie si les données existent déjà
- Si elles existent, elle les ignore (pas de doublons)
- Vous pouvez réexécuter la commande sans problème

**Note :** Pour vider et recréer, vous devez d'abord vider la base manuellement.

---

## 🔐 Connexion aux Utilisateurs

Tous les utilisateurs créés ont le même mot de passe : **`password123`**

**Exemples de connexion :**
```json
POST /api/login
{
  "email": "alice@example.com",
  "password": "password123"
}
```

```json
POST /api/login
{
  "email": "bob@example.com",
  "password": "password123"
}
```

---

## 📈 Statistiques Après Chargement

- **7 badges** créés
- **5 quêtes** créées
- **24 tags** créés
- **15 utilisateurs** créés
- **~8 matches** créés
- **~20-30 messages** créés
- **~15-60 notifications** créées
- **~15 match requests** créées

---

## ✅ Vérification

Après avoir exécuté la commande, vous pouvez vérifier :

```bash
# Voir les utilisateurs
php bin/console doctrine:query:sql 'SELECT id, email, username, level, xp FROM `user`'

# Voir les badges
php bin/console doctrine:query:sql 'SELECT id, code, name FROM badge'

# Voir les matches
php bin/console doctrine:query:sql 'SELECT id, user_a_id, user_b_id FROM user_match'
```

---

## 🎯 Utilisation pour les Tests

Ces données sont parfaites pour :
- ✅ Tester l'API avec Postman
- ✅ Tester le système de gamification
- ✅ Tester les matches et messages
- ✅ Tester les notifications
- ✅ Tester les recommandations
- ✅ Tester le leaderboard

---

**Commande à exécuter :**
```bash
php bin/console app:load-fixtures
```

**C'est tout ! 🎉**

