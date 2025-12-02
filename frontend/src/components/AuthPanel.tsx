import { useState } from 'react';
import { Mail, Shield, Smartphone, UserPlus } from 'lucide-react';

const tabs: Array<{ key: 'login' | 'signup'; label: string; helper: string }> = [
  { key: 'login', label: 'Connexion', helper: 'Reviens sur ta session' },
  { key: 'signup', label: 'Créer un compte', helper: '2 minutes pour lancer ta vibe' }
];

export function AuthPanel() {
  const [active, setActive] = useState<'login' | 'signup'>('signup');

  return (
    <section className="glass-card auth-panel">
      <header className="section-header">
        <div>
          <p>Authentification</p>
          <h2>Rejoins la communauté ou continue ton aventure</h2>
        </div>
        <span className="pill ghost">
          <Shield size={16} />
          Safe data
        </span>
      </header>

      <div className="auth-tabs">
        {tabs.map((tab) => (
          <button
            key={tab.key}
            className={`auth-tab ${tab.key === active ? 'active' : ''}`}
            onClick={() => setActive(tab.key)}
            type="button"
          >
            <span>{tab.label}</span>
            <small>{tab.helper}</small>
          </button>
        ))}
      </div>

      <form className="auth-form">
        {active === 'signup' ? (
          <label className="field">
            <span>Prénom</span>
            <input type="text" placeholder="ex : Salomé" required />
          </label>
        ) : null}

        <label className="field">
          <span>Email</span>
          <input type="email" placeholder="toi@email.com" required />
        </label>

        <label className="field">
          <span>Mot de passe</span>
          <input type="password" placeholder="••••••••" required />
        </label>

        <label className="field inline">
          <input type="checkbox" defaultChecked />
          <span>
            J’accepte la charte d’inclusivité, le chiffrement des messages et je veux recevoir les nouveautés.
          </span>
        </label>

        <div className="auth-actions">
          <button type="submit" className="btn primary full">
            <UserPlus size={18} />
            {active === 'signup' ? 'Créer mon avatar' : 'Me connecter'}
          </button>
          <button type="button" className="btn ghost full">
            <Smartphone size={18} />
            Continuer avec le mobile
          </button>
        </div>
      </form>

      <div className="auth-footnote">
        <Mail size={18} />
        <p>
          Aucun match n’est rendu public sans ton accord. Tu peux exporter / supprimer ton profil depuis ton espace
          paramètres à tout moment.
        </p>
      </div>
    </section>
  );
}

