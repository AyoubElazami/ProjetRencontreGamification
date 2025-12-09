# 📊 Diagramme du Processus de Matching

## 🎯 Exemple Concret : Alice et Bob

### **Scénario 1 : Match Réussi** ✅

```
┌─────────────────────────────────────────────────────────────┐
│                    ÉTAPE 1 : Alice like Bob                  │
└─────────────────────────────────────────────────────────────┘

👤 Alice (ID: 1)                    👤 Bob (ID: 2)
   │                                    │
   │  POST /api/users/2/like            │
   ├───────────────────────────────────>│
   │                                    │
   │  Backend vérifie :                │
   │  ✓ Pas de requête existante       │
   │  ✓ Pas de match existant          │
   │                                    │
   │  Crée MatchRequest :              │
   │  - fromUser: Alice (1)            │
   │  - toUser: Bob (2)                │
   │  - type: "like"                   │
   │  - status: "pending"              │
   │                                    │
   │  Vérifie requête réciproque :     │
   │  ❌ Bob n'a pas encore liké Alice │
   │                                    │
   │  Réponse :                        │
   │  {                                │
   │    "success": true,               │
   │    "matchRequest": {             │
   │      "status": "pending"          │
   │    }                              │
   │  }                                │
   │<───────────────────────────────────┤
   │                                    │
   │  Frontend : Profil de Bob disparaît│
   │                                    │

┌─────────────────────────────────────────────────────────────┐
│                    ÉTAPE 2 : Bob like Alice                  │
└─────────────────────────────────────────────────────────────┘

👤 Alice (ID: 1)                    👤 Bob (ID: 2)
   │                                    │
   │                                    │  POST /api/users/1/like
   │<───────────────────────────────────┤
   │                                    │
   │  Backend vérifie :                │
   │  ✓ Une requête existe (Alice→Bob) │
   │  ✓ Pas de match existant          │
   │                                    │
   │  Crée MatchRequest :              │
   │  - fromUser: Bob (2)              │
   │  - toUser: Alice (1)              │
   │  - type: "like"                   │
   │  - status: "pending"              │
   │                                    │
   │  ⚡ VÉRIFICATION RÉCIPROQUE :      │
   │  ✅ Alice a déjà liké Bob !       │
   │  ✅ Type = "like" (valide)        │
   │                                    │
   │  🎉 CRÉATION DU MATCH !           │
   │  - Crée UserMatch                 │
   │  - Met à jour les requêtes        │
   │  - Dispatch MatchCreatedEvent     │
   │                                    │
   │  Réponse :                        │
   │  {                                │
   │    "success": true,               │
   │    "matchRequest": {             │
   │      "status": "matched" ✅       │
   │    }                              │
   │  }                                │
   │───────────────────────────────────>│
   │                                    │
   │  Frontend :                       │
   │  - Alert "🎉 Match !"             │
   │  - Notification créée             │
   │  - +20 XP pour Alice et Bob       │
   │  - Match apparaît dans MatchGrid  │
```

---

## 📋 État de la Base de Données

### **Avant le Match**

```sql
-- Table: match_request
┌────┬──────────────┬────────────┬──────┬──────────┐
│ id │ from_user_id │ to_user_id │ type │  status  │
├────┼──────────────┼────────────┼──────┼──────────┤
│ 1  │ 1 (Alice)   │ 2 (Bob)    │ like │ pending  │
└────┴──────────────┴────────────┴──────┴──────────┘

-- Table: user_match
(VIDE)
```

### **Après le Match**

```sql
-- Table: match_request
┌────┬──────────────┬────────────┬──────┬──────────┐
│ id │ from_user_id │ to_user_id │ type │  status  │
├────┼──────────────┼────────────┼──────┼──────────┤
│ 1  │ 1 (Alice)   │ 2 (Bob)    │ like │ matched ✅│
│ 2  │ 2 (Bob)     │ 1 (Alice)  │ like │ matched ✅│
└────┴──────────────┴────────────┴──────┴──────────┘

-- Table: user_match
┌────┬───────────┬───────────┬─────────────────────┐
│ id │ user_a_id │ user_b_id │     created_at      │
├────┼───────────┼───────────┼─────────────────────┤
│ 1  │ 1 (Alice) │ 2 (Bob)  │ 2025-01-15 10:30:00 │
└────┴───────────┴───────────┴─────────────────────┘

-- Table: notification
┌────┬─────────┬──────────────┬─────────────────────────────┐
│ id │ user_id │     type     │         payload             │
├────┼─────────┼──────────────┼─────────────────────────────┤
│ 1  │ 1       │ match_created│ {"matchId": 1, "userId": 2}│
│ 2  │ 2       │ match_created│ {"matchId": 1, "userId": 1}│
└────┴─────────┴──────────────┴─────────────────────────────┘
```

---

## 🔄 Flux Complet avec Événements

```
┌─────────────────────────────────────────────────────────────┐
│                    MatchService::createMatchRequest()        │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
                    ┌─────────────────┐
                    │ Vérifications   │
                    │ - Requête existe?│
                    │ - Match existe? │
                    └────────┬────────┘
                             │
                             ▼
                    ┌─────────────────┐
                    │ Créer MatchRequest│
                    │ status: pending │
                    └────────┬────────┘
                             │
                             ▼
                    ┌─────────────────┐
                    │ Vérifier requête│
                    │ réciproque      │
                    └────────┬────────┘
                             │
                ┌────────────┴────────────┐
                │                         │
         ❌ Pas de requête        ✅ Requête existe
                │                         │
                │                         ▼
                │              ┌──────────────────┐
                │              │ createMatch()    │
                │              └────────┬─────────┘
                │                       │
                │                       ▼
                │              ┌──────────────────┐
                │              │ UserMatch créé   │
                │              └────────┬─────────┘
                │                       │
                │                       ▼
                │              ┌──────────────────┐
                │              │ MatchCreatedEvent│
                │              │ dispatché        │
                │              └────────┬─────────┘
                │                       │
                │                       ▼
                │              ┌──────────────────┐
                │              │ MatchCreatedSub- │
                │              │ scriber écoute   │
                │              └────────┬─────────┘
                │                       │
                │                       ▼
                │        ┌───────────────────────────┐
                │        │ Actions automatiques :    │
                │        │ - +20 XP pour chaque user │
                │        │ - Notifications créées    │
                │        │ - Badges vérifiés         │
                │        │ - Quêtes mises à jour     │
                │        └───────────────────────────┘
                │
                ▼
        Retourner MatchRequest
        (status: pending ou matched)
```

---

## 🎮 Types d'Actions et Résultats

```
┌─────────────┬──────────────┬─────────────────┬──────────────┐
│   Action    │     Type     │  Crée un match? │   Bonus XP   │
├─────────────┼──────────────┼─────────────────┼──────────────┤
│ ❤️ Like     │ "like"       │ ✅ Oui (si      │ 0 (au like)  │
│             │              │    réciproque)  │ +20 (au match)│
├─────────────┼──────────────┼─────────────────┼──────────────┤
│ ❌ Dislike  │ "dislike"    │ ❌ Non          │ 0            │
├─────────────┼──────────────┼─────────────────┼──────────────┤
│ ⭐ Superlike│ "superlike"  │ ✅ Oui (si      │ +10 (action) │
│             │              │    réciproque)  │ +20 (au match)│
├─────────────┼──────────────┼─────────────────┼──────────────┤
│ 👁️ Wink     │ "wink"       │ ❌ Non          │ 0            │
└─────────────┴──────────────┴─────────────────┴──────────────┘
```

---

## ⏱️ Timeline Détaillée

```
10:00:00 ─────────────────────────────────────────────────────
         │ Alice se connecte
         │ Frontend : GET /api/recommendations
         │ Backend : Retourne liste d'utilisateurs
         │          (exclut ceux déjà likés/matchés)
         │
10:05:00 ─────────────────────────────────────────────────────
         │ Alice voit Bob dans SwipeDeck
         │
10:05:30 ─────────────────────────────────────────────────────
         │ Alice clique sur "Matcher" (Like)
         │ Frontend : POST /api/users/2/like
         │
         │ Backend :
         │   1. Vérifie : Pas de requête existante ✓
         │   2. Vérifie : Pas de match existant ✓
         │   3. Crée MatchRequest #1 :
         │      - fromUser: Alice (1)
         │      - toUser: Bob (2)
         │      - type: "like"
         │      - status: "pending"
         │   4. Vérifie requête réciproque :
         │      - Bob n'a pas liké Alice ❌
         │   5. Retourne : {status: "pending"}
         │
         │ Frontend :
         │   - Profil de Bob disparaît du SwipeDeck
         │   - Pas d'alerte (pas de match)
         │
10:10:00 ─────────────────────────────────────────────────────
         │ Bob se connecte
         │ Frontend : GET /api/recommendations
         │ Backend : Retourne liste (exclut Alice car requête pending)
         │          OU inclut Alice si pas encore filtré
         │
10:10:15 ─────────────────────────────────────────────────────
         │ Bob voit Alice dans SwipeDeck
         │
10:10:45 ─────────────────────────────────────────────────────
         │ Bob clique sur "Matcher" (Like)
         │ Frontend : POST /api/users/1/like
         │
         │ Backend :
         │   1. Vérifie : Une requête existe (Alice→Bob) ✓
         │   2. Crée MatchRequest #2 :
         │      - fromUser: Bob (2)
         │      - toUser: Alice (1)
         │      - type: "like"
         │      - status: "pending"
         │   3. ⚡ VÉRIFICATION RÉCIPROQUE :
         │      - Alice a déjà liké Bob ✅
         │      - Type = "like" (valide) ✅
         │   4. 🎉 CRÉATION DU MATCH :
         │      - Crée UserMatch #1
         │      - Met MatchRequest #1 → status: "matched"
         │      - Met MatchRequest #2 → status: "matched"
         │   5. Dispatch MatchCreatedEvent
         │
         │ MatchCreatedSubscriber :
         │   - +20 XP pour Alice
         │   - +20 XP pour Bob
         │   - Notification pour Alice
         │   - Notification pour Bob
         │   - Vérification badges/quests
         │
         │ Frontend :
         │   - Alert "🎉 Match ! Vous avez un nouveau match !"
         │   - Profil d'Alice disparaît
         │   - Match apparaît dans MatchGrid
         │   - Notification affichée
         │
10:11:00 ─────────────────────────────────────────────────────
         │ Alice et Bob peuvent maintenant :
         │ - Voir le match dans MatchGrid
         │ - Cliquer pour ouvrir le chat
         │ - Envoyer des messages
         │ - Voir leurs notifications
```

---

## 🔍 Détails Techniques

### **Vérification Réciproque (Ligne 46 de MatchService.php)**

```php
// Vérifie si Bob a déjà liké Alice
$reciprocalRequest = $this->matchRequestRepo->findPendingLike($toUser, $fromUser);
// $toUser = Bob, $fromUser = Alice
// Cherche : Bob → Alice, type = like/superlike, status = pending

if ($reciprocalRequest && in_array($matchRequest->getType(), [MatchRequest::TYPE_LIKE, MatchRequest::TYPE_SUPERLIKE])) {
    // ✅ MATCH DÉTECTÉ !
    $this->createMatch($fromUser, $toUser);
    // ...
}
```

### **Création du Match (Ligne 59-72)**

```php
public function createMatch(User $userA, User $userB): UserMatch
{
    // 1. Créer l'entité UserMatch
    $match = new UserMatch();
    $match->setUserA($userA);  // Alice
    $match->setUserB($userB);  // Bob
    
    // 2. Sauvegarder en base
    $this->em->persist($match);
    $this->em->flush();
    
    // 3. Dispatcher l'événement
    $event = new MatchCreatedEvent($match);
    $this->eventDispatcher->dispatch($event, MatchCreatedEvent::NAME);
    
    return $match;
}
```

### **Événement MatchCreated (MatchCreatedSubscriber)**

Quand un match est créé, automatiquement :

1. **+20 XP** pour chaque utilisateur
2. **Notification** créée pour chaque utilisateur
3. **Score** mis à jour (leaderboard)
4. **Badges** vérifiés (ex: "First Match")
5. **Quêtes** mises à jour (ex: "Match with 5 people")

---

## 🎯 Points Clés à Retenir

1. ✅ **Match = Like réciproque** : Les deux doivent liker
2. ✅ **Match instantané** : Si l'autre a déjà liké, match immédiat
3. ✅ **Match différé** : Si l'autre like plus tard, match créé à ce moment
4. ❌ **Dislike = Pas de match** : Même si l'autre a liké
5. ⭐ **Superlike = Like amélioré** : Fonctionne comme un like mais avec bonus XP
6. 🔒 **Protection** : Impossible de liker deux fois, impossible de créer un match si un match existe déjà

---

## 📱 Expérience Utilisateur

### **Côté Alice (qui like en premier)**

1. Voit Bob dans SwipeDeck
2. Clique sur "Matcher"
3. Profil de Bob disparaît
4. **Pas de notification** (pas encore de match)
5. Plus tard : Si Bob like → Notification "🎉 Match !"

### **Côté Bob (qui like en second)**

1. Voit Alice dans SwipeDeck
2. Clique sur "Matcher"
3. **Alert immédiat** : "🎉 Match ! Vous avez un nouveau match !"
4. Notification créée
5. Match apparaît dans MatchGrid
6. Peut chatter immédiatement

---

## 🔄 Cas Spéciaux

### **Cas 1 : Alice like Bob, puis Bob dislike Alice**

```
Alice → Like → Bob    (requête #1, pending)
Bob → Dislike → Alice (requête #2, pending)

Résultat : ❌ Pas de match
Les deux requêtes restent en "pending"
```

### **Cas 2 : Alice superlike Bob, puis Bob like Alice**

```
Alice → Superlike → Bob (requête #1, pending, +10 XP pour Alice)
Bob → Like → Alice     (requête #2, pending)

Vérification réciproque :
✅ Alice a superliké Bob (type = superlike, valide)
✅ Match créé !
✅ +20 XP pour Alice et Bob
✅ Requêtes mises à "matched"
```

### **Cas 3 : Alice like Bob deux fois**

```
Alice → Like → Bob (requête #1 créée)
Alice → Like → Bob (tentative)

Résultat : ❌ Erreur "A match request already exists"
```

---

## 🎁 Récompenses

| Action | XP gagné | Quand |
|--------|----------|-------|
| Like | 0 | Au moment du like |
| Superlike | +10 | Au moment du superlike |
| Match | +20 | Quand le match est créé |
| Message | +5 | À chaque message envoyé |

---

## 📊 Statistiques

Après un match entre Alice et Bob :

- **Alice** :
  - +20 XP (match)
  - +10 XP si superlike utilisé
  - Niveau peut augmenter
  - Badge "First Match" possible
  - Quête "Match with someone" progressée

- **Bob** :
  - +20 XP (match)
  - Niveau peut augmenter
  - Badge "First Match" possible
  - Quête "Match with someone" progressée

---

## 🔐 Sécurité

Le système empêche :

1. ✅ **Double requête** : Impossible de liker deux fois
2. ✅ **Auto-like** : Impossible de se liker soi-même
3. ✅ **Match existant** : Impossible de créer un match si un match existe déjà
4. ✅ **Requête existante** : Impossible de créer une requête si une requête pending existe déjà

---

## 📝 Résumé en 3 Points

1. **Like = Requête en attente** : L'autre ne le sait pas encore
2. **Like réciproque = Match** : Les deux peuvent chatter
3. **Dislike = Pas de match** : Même si l'autre a liké

