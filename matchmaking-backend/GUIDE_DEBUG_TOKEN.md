# 🔍 Guide de débogage du token JWT

## Problème actuel

Vous recevez :
```json
{
  "code": 401,
  "message": "User not found: TestUser"
}
```

Cela signifie que votre **token actuel contient "TestUser"** au lieu d'un email.

## Solution : Obtenir un NOUVEAU token

### ⚠️ IMPORTANT : Vous devez vous reconnecter !

Votre token actuel est **invalide** ou **ancien**. Vous devez obtenir un **nouveau token** en vous reconnectant.

### Étape 1 : Se reconnecter

**POST** `http://localhost:8000/api/login`

**Body (raw JSON):**
```json
{
  "email": "test@example.com",
  "password": "votre_password"
}
```

**→ COPIEZ LE NOUVEAU TOKEN DE LA RÉPONSE !**

### Étape 2 : Vérifier le nouveau token

Allez sur **https://jwt.io** et collez votre **NOUVEAU token**.

**Le payload devrait maintenant contenir :**
```json
{
  "username": "test@example.com",  ← EMAIL (pas "TestUser")
  "iat": 1234567890,
  "exp": 1234571490
}
```

**Si vous voyez encore :**
```json
{
  "username": "TestUser",  ← ❌ PROBLÈME
  ...
}
```

Alors il y a un problème avec la création du token.

### Étape 3 : Utiliser le nouveau token

**POST** `http://localhost:8000/api/user/welcome`

**Dans Postman :**
1. Onglet **Authorization**
2. Type : **Bearer Token**
3. Token : Collez le **NOUVEAU token** (pas l'ancien !)

---

## Pourquoi ce problème ?

LexikJWTBundle stocke `getUserIdentifier()` dans le champ `"username"` du token.

Dans votre entité `User`, `getUserIdentifier()` retourne l'email :
```php
public function getUserIdentifier(): string { 
    return (string) $this->email; 
}
```

Donc le token devrait contenir l'**email**, pas "TestUser".

---

## Checklist

- [ ] Vous avez obtenu un **nouveau token** en vous reconnectant
- [ ] Le nouveau token contient un **email** dans "username" (vérifié sur jwt.io)
- [ ] Vous utilisez le **nouveau token** (pas l'ancien)
- [ ] Le token est complet (très long)

---

**Reconnectez-vous maintenant et utilisez le NOUVEAU token !** 🔄



