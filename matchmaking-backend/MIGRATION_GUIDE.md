# Guide d'exécution de la migration

## 📋 Migration créée

Le fichier de migration `Version20251202110000.php` a été créé dans le dossier `migrations/`.

Cette migration va créer toutes les tables suivantes :
- ✅ **profile_tag** - Tags pour les profils utilisateurs
- ✅ **user_profile_tag** - Table de liaison User ↔ ProfileTag
- ✅ **match_request** - Requêtes de like/dislike/superlike/wink
- ✅ **user_match** - Matches mutuels entre utilisateurs
- ✅ **message** - Messages dans les conversations
- ✅ **badge** - Badges de gamification
- ✅ **user_badge** - Association User-Badge
- ✅ **quest** - Quêtes
- ✅ **user_quest** - Progression des quêtes
- ✅ **notification** - Notifications système
- ✅ **report** - Rapports de modération

Et mettre à jour la table **user** avec les nouveaux champs :
- gender, age, location, latitude, longitude
- preferences (JSON)
- score, avatar_url
- created_at, updated_at
- bio modifié en TEXT

## 🚀 Commandes pour exécuter la migration

### 1. Vérifier l'état des migrations

```bash
cd matchmaking-backend
php bin/console doctrine:migrations:status
```

### 2. Voir la migration à exécuter

```bash
php bin/console doctrine:migrations:list
```

### 3. Exécuter la migration

```bash
php bin/console doctrine:migrations:migrate
```

Vous serez invité à confirmer. Tapez `yes` ou appuyez sur Entrée.

### 4. (Optionnel) Exécuter une migration spécifique

```bash
php bin/console doctrine:migrations:execute --up DoctrineMigrations\\Version20251202110000
```

## ⚠️ Important

### Si certaines colonnes de la table `user` existent déjà

Si vous obtenez une erreur indiquant qu'une colonne existe déjà dans la table `user`, vous avez deux options :

#### Option 1: Commenter les lignes correspondantes

Ouvrez `migrations/Version20251202110000.php` et commentez les lignes qui créent les colonnes existantes.

Par exemple, si `gender` existe déjà :
```php
// $this->addSql('ALTER TABLE `user` ADD gender VARCHAR(20) DEFAULT NULL');
```

#### Option 2: Vérifier manuellement

Vérifiez quelles colonnes existent déjà dans votre table `user` et ajustez la migration en conséquence.

### Si vous voulez revenir en arrière

Pour annuler la migration :

```bash
php bin/console doctrine:migrations:migrate prev
```

Ou pour revenir à une version spécifique :

```bash
php bin/console doctrine:migrations:migrate DoctrineMigrations\\Version20251125120256
```

## 📊 Vérifier que les tables ont été créées

Après l'exécution de la migration, vérifiez les tables dans votre base de données :

```bash
php bin/console doctrine:schema:validate
```

Ou connectez-vous directement à votre base de données et listez les tables :

```sql
SHOW TABLES;
```

## 🔄 Si vous voulez régénérer la migration automatiquement

Si vous préférez que Doctrine génère automatiquement la migration en comparant vos entités avec la base de données :

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

**Note:** Cette commande peut créer une migration différente si votre base de données n'est pas à jour.

## ✅ Après l'exécution

Une fois la migration exécutée avec succès :

1. ✅ Toutes les tables seront créées
2. ✅ La table `user` sera mise à jour avec les nouveaux champs
3. ✅ Vous pourrez utiliser toutes les fonctionnalités de l'API

## 🐛 En cas d'erreur

### Erreur de connexion à la base de données

Vérifiez votre fichier `.env` :
```env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/matchmaking?serverVersion=8.0&charset=utf8mb4"
```

Ou pour PostgreSQL :
```env
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/matchmaking?serverVersion=16&charset=utf8"
```

### Erreur de permissions

Assurez-vous que votre utilisateur de base de données a les droits nécessaires :
- CREATE
- ALTER
- INDEX
- FOREIGN KEY

### Erreur de syntaxe SQL

La migration utilise la syntaxe MySQL/MariaDB. Si vous utilisez PostgreSQL, vous devrez peut-être adapter certaines commandes.

## 📝 Exemple de commandes complètes

```bash
# 1. Aller dans le dossier du backend
cd matchmaking-backend

# 2. Vider le cache
php bin/console cache:clear

# 3. Vérifier l'état
php bin/console doctrine:migrations:status

# 4. Exécuter la migration
php bin/console doctrine:migrations:migrate

# 5. Vérifier que tout est OK
php bin/console doctrine:schema:validate
```

Bonne migration ! 🚀

