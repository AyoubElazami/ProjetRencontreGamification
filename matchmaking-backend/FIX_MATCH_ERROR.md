# Correction de l'erreur "unexpected token 'match'"

## Problème

L'erreur `syntax error, unexpected token "match", expecting variable` se produit parce que `match` est un mot-clé réservé en PHP 8.0+ (pour les expressions match) et peut causer des conflits.

## Solutions appliquées

1. ✅ Renommage de la table de `match` à `user_match` dans `Match.php`
2. ✅ Utilisation du namespace complet dans l'attribut Doctrine de `Message.php`

## Étapes pour résoudre le problème

### 1. Vider le cache Symfony

```bash
cd matchmaking-backend
php bin/console cache:clear
```

### 2. Vérifier que les fichiers sont corrects

Assurez-vous que :
- `src/Entity/Match.php` utilise `#[ORM\Table(name: "user_match")]`
- `src/Entity/Message.php` utilise `targetEntity: "App\Entity\Match"` avec le namespace complet

### 3. Si le problème persiste

Videz également le cache de Doctrine :

```bash
php bin/console doctrine:cache:clear-metadata
php bin/console doctrine:cache:clear-query
php bin/console doctrine:cache:clear-result
```

### 4. Régénérer les métadonnées

```bash
php bin/console cache:warmup
```

### 5. Vérifier la syntaxe PHP

```bash
php -l src/Entity/Match.php
php -l src/Entity/Message.php
php -l src/Entity/User.php
```

## Si le problème persiste toujours

Il peut être nécessaire de renommer complètement la classe `Match` en `UserMatch` pour éviter tout conflit avec le mot-clé `match` de PHP 8.0+.

Mais d'abord, essayez les solutions ci-dessus !

