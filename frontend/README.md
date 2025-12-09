# Frontend Matchmaking App

Application frontend Next.js avec TypeScript pour l'application de matchmaking.

## 🚀 Démarrage rapide

### Installation

```bash
npm install
```

### Configuration

Créez un fichier `.env.local` à la racine du projet :

```env
NEXT_PUBLIC_API_URL=http://localhost:8000
```

### Lancement

```bash
npm run dev
```

L'application sera accessible sur [http://localhost:3000](http://localhost:3000)

## 📁 Structure du projet

```
frontend/
├── app/                    # Pages Next.js (App Router)
│   ├── page.tsx           # Page d'accueil (login)
│   ├── dashboard/         # Dashboard principal
│   ├── matches/[id]/      # Page de chat/match
│   ├── profile/           # Page de profil
│   └── leaderboard/       # Page de classement
├── components/            # Composants React réutilisables
│   ├── SwipeDeck.tsx     # Deck de swipe avec like/dislike/superlike
│   ├── MatchGrid.tsx     # Grille des matches
│   ├── UserStatus.tsx    # Statut utilisateur (header)
│   ├── Notifications.tsx # Système de notifications
│   └── ...
├── contexts/              # Contextes React
│   └── AuthContext.tsx   # Contexte d'authentification
├── lib/                   # Utilitaires et services
│   └── api.ts            # Services API REST
└── ...
```

## 🎯 Fonctionnalités

### ✅ Authentification
- Login / Register
- Gestion du token JWT
- Protection des routes

### ✅ Matchmaking
- SwipeDeck avec recommandations
- Like / Dislike / Superlike
- Système de matches
- Chat en temps réel

### ✅ Profil
- Édition du profil
- Upload d'avatar
- Gestion des préférences

### ✅ Gamification
- Système de niveaux et XP
- Badges
- Quêtes
- Leaderboard

### ✅ Notifications
- Notifications en temps réel
- Marquer comme lu
- Compteur de non lus

## 🔌 API Backend

L'application se connecte au backend Symfony sur `http://localhost:8000` par défaut.

Toutes les routes API sont définies dans `lib/api.ts` :
- `authAPI` - Authentification
- `userAPI` - Gestion utilisateurs
- `matchAPI` - Matchmaking et messages
- `gamificationAPI` - Gamification
- `notificationAPI` - Notifications

## 🎨 Design

- Design moderne avec glassmorphism
- Thème sombre avec gradients
- Responsive (mobile-first)
- Animations avec Framer Motion

## 📝 Compte de test

- Email: `alice@example.com`
- Password: `password123`

## 🛠️ Technologies

- **Next.js 16** - Framework React
- **TypeScript** - Typage statique
- **Tailwind CSS** - Styling
- **Axios** - Client HTTP
- **Framer Motion** - Animations
- **Lucide React** - Icônes
