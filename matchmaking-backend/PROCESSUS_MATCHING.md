# 🎯 Processus de Matching - Guide Complet

## 📋 Vue d'ensemble

Le système de matching fonctionne avec un système de **requêtes bidirectionnelles** : un match se crée uniquement quand **deux utilisateurs se likent mutuellement**.

---

## 🔄 Flux de Matching (Exemple : Alice et Bob)

### **Scénario 1 : Match Réussi** ✅

#### **Étape 1 : Alice like Bob**
```
👤 Alice → ❤️ Like → 👤 Bob
```

**Actions backend :**
1. Vérification : Pas de requête existante entre Alice et Bob
2. Création d'une `MatchRequest` :
   - `fromUser`: Alice
   - `toUser`: Bob
   - `type`: `like`
   - `status`: `pending`
3. Vérification réciproque : Bob a-t-il déjà liké Alice ?
   - ❌ Non → La requête reste en `pending`
   - ✅ Oui → **MATCH IMMÉDIAT !**

**Réponse API :**
```json
{
  "success": true,
  "matchRequest": {
    "id": 1,
    "type": "like",
    "status": "pending"  // ou "matched" si match immédiat
  }
}
```

**État de la base de données :**
```sql
-- Table: match_request
id | from_user_id | to_user_id | type | status
1  | Alice (1)    | Bob (2)    | like | pending

-- Table: user_match
(VIDE - pas encore de match)
```

---

#### **Étape 2 : Bob like Alice** (Match !)
```
👤 Bob → ❤️ Like → 👤 Alice
```

**Actions backend :**
1. Vérification : Une requête existe déjà entre Bob et Alice
2. Création d'une nouvelle `MatchRequest` :
   - `fromUser`: Bob
   - `toUser`: Alice
   - `type`: `like`
   - `status`: `pending`
3. **Vérification réciproque** : Alice a déjà liké Bob ?
   - ✅ **OUI !** → **MATCH DÉTECTÉ !**
4. Création d'un `UserMatch` :
   - `userA`: Alice
   - `userB`: Bob
   - `createdAt`: maintenant
5. Mise à jour des requêtes :
   - Requête Alice→Bob : `status` = `matched`
   - Requête Bob→Alice : `status` = `matched`
6. **Événement déclenché** : `MatchCreatedEvent`
   - Création de notifications pour Alice et Bob
   - Ajout de XP (20 XP par match)
   - Vérification des badges/quests

**Réponse API :**
```json
{
  "success": true,
  "matchRequest": {
    "id": 2,
    "type": "like",
    "status": "matched"  // ✅ MATCH !
  }
}
```

**État de la base de données :**
```sql
-- Table: match_request
id | from_user_id | to_user_id | type | status
1  | Alice (1)    | Bob (2)    | like | matched  ✅
2  | Bob (2)      | Alice (1)  | like | matched  ✅

-- Table: user_match
id | user_a_id | user_b_id | created_at
1  | Alice (1) | Bob (2)   | 2025-01-15 10:30:00  ✅

-- Table: notification (créées automatiquement)
id | user_id | type           | payload
1  | Alice   | match_created  | {"matchId": 1, "userId": 2}
2  | Bob     | match_created  | {"matchId": 1, "userId": 1}
```

---

### **Scénario 2 : Pas de Match** ❌

#### **Étape 1 : Alice like Bob**
```
👤 Alice → ❤️ Like → 👤 Bob
```
- Requête créée avec `status: pending`
- Bob ne sait pas encore qu'Alice l'a liké

#### **Étape 2 : Bob dislike Alice**
```
👤 Bob → ❌ Dislike → 👤 Alice
```
- Nouvelle requête créée : `type: dislike`, `status: pending`
- **Pas de match** car Bob a disliké
- Les deux requêtes restent en `pending`

**État final :**
```sql
-- Table: match_request
id | from_user_id | to_user_id | type    | status
1  | Alice (1)    | Bob (2)    | like    | pending
2  | Bob (2)      | Alice (1)  | dislike | pending

-- Table: user_match
(VIDE - pas de match)
```

---

### **Scénario 3 : Superlike** ⭐

#### **Étape 1 : Alice superlike Bob**
```
👤 Alice → ⭐ Superlike → 👤 Bob
```
- Requête créée : `type: superlike`, `status: pending`
- **Bonus XP** : +10 XP pour Alice
- Si Bob like/superlike Alice ensuite → **MATCH !**

---

## 🎮 Types d'Actions Disponibles

| Action | Type | Peut créer un match ? | XP gagné |
|--------|------|----------------------|----------|
| **Like** | `like` | ✅ Oui | 0 (au like) |
| **Dislike** | `dislike` | ❌ Non | 0 |
| **Superlike** | `superlike` | ✅ Oui | +10 XP |
| **Wink** | `wink` | ❌ Non | 0 |

---

## 🔍 Conditions pour un Match

Un match se crée **UNIQUEMENT** si :

1. ✅ **Utilisateur A** like/superlike **Utilisateur B**
2. ✅ **Utilisateur B** like/superlike **Utilisateur A** (dans l'ordre ou après)
3. ✅ Les deux actions sont de type `like` ou `superlike` (pas `dislike` ou `wink`)

**Important :**
- Un `dislike` ne crée **jamais** de match
- Un `wink` ne crée **jamais** de match
- Un match nécessite **deux likes réciproques**

---

## 📊 États des Requêtes

### `STATUS_PENDING`
- Requête créée, en attente d'une action réciproque
- L'utilisateur cible ne sait pas encore (pas de notification)

### `STATUS_MATCHED`
- Les deux utilisateurs se sont likés
- Un `UserMatch` a été créé
- Les deux utilisateurs peuvent maintenant chatter

### `STATUS_UNMATCHED`
- (Non utilisé actuellement, pour futures fonctionnalités)

---

## 🎁 Conséquences d'un Match

Quand un match est créé :

1. **Création du UserMatch**
   - Les deux utilisateurs peuvent maintenant chatter
   - Le match apparaît dans `/api/matches`

2. **Notifications**
   - Notification pour Alice : "Vous avez un nouveau match avec Bob !"
   - Notification pour Bob : "Vous avez un nouveau match avec Alice !"

3. **Gamification**
   - +20 XP pour chaque utilisateur
   - Vérification des badges (ex: "First Match", "10 Matches")
   - Mise à jour des quêtes (ex: "Match with 5 people")

4. **Événement**
   - `MatchCreatedEvent` est dispatché
   - Permet d'ajouter des listeners pour des actions personnalisées

---

## 🔄 Exemple Complet : Timeline

```
10:00:00 - Alice se connecte
10:05:00 - Alice voit Bob dans ses recommandations
10:05:30 - Alice like Bob
          → MatchRequest #1 créée (Alice→Bob, pending)
          → Base de données mise à jour
          → Frontend : Profil de Bob disparaît

10:10:00 - Bob se connecte
10:10:15 - Bob voit Alice dans ses recommandations
10:10:45 - Bob like Alice
          → MatchRequest #2 créée (Bob→Alice, pending)
          → Détection : Alice a déjà liké Bob !
          → ✅ MATCH CRÉÉ !
          → UserMatch créé
          → Notifications envoyées
          → +20 XP pour Alice et Bob
          → Frontend : Alert "🎉 Match !"

10:11:00 - Alice et Bob peuvent maintenant chatter
          → `/api/matches` retourne le match
          → Page de chat accessible
```

---

## 🛡️ Protections

Le système empêche :

1. **Double requête** : Impossible de liker deux fois la même personne
2. **Auto-like** : Impossible de se liker soi-même
3. **Match existant** : Impossible de créer un match si un match existe déjà
4. **Requête existante** : Impossible de créer une requête si une requête pending existe déjà

---

## 📱 Expérience Utilisateur

### Côté Frontend

1. **SwipeDeck** : L'utilisateur voit des recommandations
2. **Action** : Like/Dislike/Superlike
3. **Réponse** :
   - Si `status: pending` → Profil disparaît, pas de notification
   - Si `status: matched` → Alert "🎉 Match !", notification, profil disparaît
4. **MatchGrid** : Les matches apparaissent automatiquement
5. **Chat** : Clic sur un match → Page de chat

---

## 🎯 Points Clés

- ✅ **Match = Like réciproque** (les deux doivent liker)
- ✅ **Match instantané** si l'autre a déjà liké
- ✅ **Match différé** si l'autre like plus tard
- ✅ **Dislike = Pas de match** (même si l'autre a liké)
- ✅ **Superlike = Like amélioré** (avec bonus XP)

---

## 🔧 Code Backend

### MatchService::createMatchRequest()

```php
1. Vérifier si requête existe déjà → Exception si oui
2. Vérifier si match existe déjà → Exception si oui
3. Créer MatchRequest avec status: pending
4. Sauvegarder en base
5. Vérifier requête réciproque :
   - Si existe ET type = like/superlike → CRÉER MATCH
   - Sinon → Retourner requête pending
```

### MatchService::createMatch()

```php
1. Créer UserMatch
2. Sauvegarder en base
3. Dispatcher MatchCreatedEvent
4. Retourner le match
```

---

## 📝 Résumé

**Le matching fonctionne comme Tinder :**
- Vous likez quelqu'un → Il/Elle ne le sait pas encore
- Si cette personne vous like aussi → **MATCH !** 🎉
- Vous pouvez maintenant chatter
- Les dislikes ne créent jamais de match

