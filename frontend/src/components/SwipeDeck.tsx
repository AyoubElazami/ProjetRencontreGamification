import { motion } from 'framer-motion';
import { Flame, Heart, Sparkles, Star, X } from 'lucide-react';
import { deckProfiles } from '../assets/mockData';

const cardVariants = {
  initial: (index: number) => ({
    rotate: index === 0 ? 0 : index === 1 ? -4 : 4,
    y: index * -12,
    zIndex: deckProfiles.length - index
  }),
  animate: {
    rotate: 0,
    y: 0,
    transition: { type: 'spring', duration: 0.5, damping: 20 }
  }
};

export function SwipeDeck() {
  return (
    <section className="glass-card swipe-section">
      <header className="section-header">
        <div>
          <p>Deck immersif</p>
          <h2>Swipe scénarisé avec actions exclusives</h2>
        </div>
        <div className="pill neon">
          <Flame size={16} />
          Boost actif 12min
        </div>
      </header>

      <div className="swipe-deck">
        {deckProfiles.map((profile, index) => (
          <motion.article
            key={profile.name}
            className="swipe-card"
            custom={index}
            variants={cardVariants}
            initial="initial"
            animate="animate"
            whileHover={{ y: -6 }}
          >
            <div className="swipe-card__image" style={{ backgroundImage: `url(${profile.image})` }}>
              <div className="swipe-card__badge">
                <Star size={16} />
                {profile.compatibility}% vibe match
              </div>
            </div>
            <div className="swipe-card__body">
              <h3>{profile.name}</h3>
              <p>{profile.headline}</p>
              <div className="swipe-card__meta">
                <span>{profile.distance}</span>
                <span>Compat {profile.compatibility}%</span>
              </div>
              <div className="tag-wrap">
                {profile.tags.map((tag) => (
                  <span key={tag} className="tag-pill">
                    {tag}
                  </span>
                ))}
              </div>
            </div>
          </motion.article>
        ))}
      </div>

      <div className="swipe-actions">
        <button className="btn ghost">
          <X size={18} />
          Passer
        </button>
        <button className="btn primary">
          <Heart size={18} />
          Matcher
        </button>
        <button className="btn accent">
          <Sparkles size={18} />
          Lancer une quête
        </button>
      </div>
    </section>
  );
}

