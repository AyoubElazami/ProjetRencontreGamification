# 🚀 Guide de Configuration et Démarrage

## 📋 Prérequis

- Node.js 18+ installé
- Backend Symfony tournant sur `http://localhost:8000`

## 🔧 Installation

1. **Installer les dépendances**
   ```bash
   cd frontend
   npm install
   ```

2. **Configurer l'environnement**
   
   Créez un fichier `.env.local` à la racine du dossier `frontend` :
   ```env
   NEXT_PUBLIC_API_URL=http://localhost:8000
   ```

## ▶️ Démarrage

### Mode développement
```bash
npm run dev
```

L'application sera accessible sur [http://localhost:3000](http://localhost:3000)

### Mode production
```bash
npm run build
npm start
```

## 🧪 Test de l'application

### Compte de test
- **Email:** `alice@example.com`
- **Password:** `password123`

### Vérifier que le backend fonctionne
Assurez-vous que le backend Symfony tourne :
```bash
cd ../matchmaking-backend
php bin/console server:start
```

## 📱 Fonctionnalités disponibles

### ✅ Authentification
- Page de login/register
- Gestion automatique du token JWT
- Protection des routes

### ✅ Dashboard
- SwipeDeck avec recommandations
- Grille des matches
- Statistiques et métriques
- Quêtes et badges

### ✅ Matchmaking
- Like / Dislike / Superlike
- Système de matches
- Chat en temps réel

### ✅ Profil
- Édition du profil
- Upload d'avatar
- Affichage des badges

### ✅ Gamification
- Leaderboard
- Système de niveaux et XP
- Quêtes
- Badges

### ✅ Navigation
- Barre de navigation en bas
- Navigation fluide entre les pages

## 🐛 Résolution de problèmes

### Erreur CORS
Si vous rencontrez des erreurs CORS, vérifiez que :
1. Le backend a le `CorsListener` configuré
2. L'URL dans `.env.local` correspond à celle du backend

### Erreur de connexion
1. Vérifiez que le backend tourne sur `http://localhost:8000`
2. Vérifiez les logs du backend
3. Vérifiez la console du navigateur (F12)

### Erreur de build
```bash
rm -rf .next node_modules
npm install
npm run dev
```

## 📚 Structure du projet

```
frontend/
├── app/              # Pages Next.js (App Router)
├── components/       # Composants React
├── contexts/         # Contextes React (Auth)
├── lib/             # Services et utilitaires
└── public/          # Fichiers statiques
```

## 🎨 Personnalisation

Les couleurs principales sont définies dans `app/globals.css` :
- Primary: `#8ad6ff` (bleu)
- Accent: `#ff7a84` (rose)
- Background: `#0a0915` (noir)

Vous pouvez les modifier dans le fichier CSS.

