import { motion } from 'framer-motion';
import { Flame, Sparkles, Zap } from 'lucide-react';

const heroStats = [
  { label: 'Caméras activées', value: '842', trend: '+24%' },
  { label: 'Quêtes sociales', value: '62', trend: 'Live' },
  { label: 'Boost actifs', value: '118', trend: 'x2' }
];

export function Hero() {
  return (
    <section className="glass-card hero-card">
      <div className="hero-header">
        <div>
          <p>Rencontre+ / Edition 2025</p>
          <h1>Des rencontres naturelles avec un coup de pouce intelligent.</h1>
          <p className="hero-lead">
            Choisis une quête, laisse l’IA te proposer des affinités émotionnelles et rejoins des rooms audio/vidéo
            chaleureuses. Pas de phrases toutes faites, juste des échanges qui ressemblent à la vraie vie.
          </p>
        </div>
        <motion.div
          className="hero-badge"
          animate={{ boxShadow: ['0 0 20px rgba(255,107,139,0.4)', '0 0 0 rgba(255,107,139,0.1)'] }}
          transition={{ duration: 2.8, repeat: Infinity }}
        >
          <Sparkles size={18} />
          <span>Invitations XR ouvertes</span>
        </motion.div>
      </div>

      <div className="hero-cta">
        <button className="btn primary">
          <Flame size={18} />
          Booster mon aura
        </button>
        <button className="btn ghost">
          <Zap size={18} />
          Entrer dans une room
        </button>
      </div>

      <div className="hero-grid">
        <motion.div
          className="hero-stream"
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
        >
          <p>Ce qui se passe maintenant</p>
          <ul>
            <li>• Salomé vient d’ouvrir la quête “Balade au canal”.</li>
            <li>• 3 rooms cosy ouvrent dans 4 min.</li>
            <li>• Boost émotionnel x2 disponible.</li>
          </ul>
        </motion.div>
        <div className="hero-stats">
          {heroStats.map((stat) => (
            <article key={stat.label}>
              <strong>{stat.value}</strong>
              <span>{stat.label}</span>
              <small>{stat.trend}</small>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
}

