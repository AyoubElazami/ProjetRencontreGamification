# Vider le cache pour résoudre l'erreur

## Problème résolu

L'erreur `syntax error, unexpected token "match"` était causée par l'utilisation de `$match` comme nom de variable dans une closure arrow function avec type hint. C'est corrigé !

## Commandes pour vider le cache

### 1. Vider le cache Symfony

```bash
cd matchmaking-backend
php bin/console cache:clear
```

### 2. Si l'erreur persiste, vider aussi les caches Doctrine

```bash
php bin/console doctrine:cache:clear-metadata
php bin/console doctrine:cache:clear-query
php bin/console doctrine:cache:clear-result
```

### 3. Réchauffer le cache

```bash
php bin/console cache:warmup
```

### 4. Maintenant vous pouvez exécuter la migration

```bash
php bin/console doctrine:migrations:migrate
```

## Correction appliquée

Dans `MatchController.php` ligne 32, j'ai changé :
```php
// AVANT (erreur)
fn(Match $match) => $this->serializeMatch($match, $currentUser)

// APRÈS (corrigé)
fn(Match $m) => $this->serializeMatch($m, $currentUser)
```

Le nom de variable `$match` entrait en conflit avec le mot-clé `match` de PHP 8.0+ dans le contexte d'un type hint de closure.

