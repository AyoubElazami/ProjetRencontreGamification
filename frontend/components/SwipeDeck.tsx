'use client';

import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Flame, Heart, Sparkles, Star, X, Loader2 } from 'lucide-react';
import { userAPI, matchAPI, User } from '@/lib/api';
import { useAuth } from '@/contexts/AuthContext';

const cardVariants = {
  initial: (index: number) => ({
    rotate: index === 0 ? 0 : index === 1 ? -4 : 4,
    y: index * -12,
    zIndex: 3 - index,
  }),
  animate: {
    rotate: 0,
    y: 0,
    transition: { type: 'spring', duration: 0.5, damping: 20 },
  },
};

export default function SwipeDeck() {
  const { user } = useAuth();
  const [profiles, setProfiles] = useState<User[]>([]);
  const [loading, setLoading] = useState(true);
  const [swiping, setSwiping] = useState(false);

  useEffect(() => {
    if (user) {
      loadRecommendations();
    }
  }, [user]);

  const loadRecommendations = async () => {
    try {
      setLoading(true);
      const response = await userAPI.getRecommendations(10);
      setProfiles(response.users);
    } catch (error) {
      console.error('Erreur lors du chargement des recommandations:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleAction = async (action: 'like' | 'dislike' | 'superlike', userId: number) => {
    if (swiping) return;

    setSwiping(true);
    try {
      let result;
      switch (action) {
        case 'like':
          result = await matchAPI.like(userId);
          break;
        case 'dislike':
          result = await matchAPI.dislike(userId);
          break;
        case 'superlike':
          result = await matchAPI.superlike(userId);
          break;
      }

      console.log('✅ Action réussie:', result);

      // Retirer le profil de la liste
      setProfiles((prev) => prev.filter((p) => p.id !== userId));

      if (result.matchRequest?.status === 'matched') {
        alert('🎉 Match ! Vous avez un nouveau match !');
        window.dispatchEvent(new CustomEvent('match-created'));
      }

      // Recharger les recommandations pour avoir de nouveaux profils
      if (profiles.length <= 3) {
        setTimeout(() => {
          loadRecommendations();
        }, 300);
      }
    } catch (error: any) {
      console.error('❌ Erreur lors de l\'action:', error);
      
      const errorMessage = error.response?.data?.error || error.message || 'Une erreur est survenue';
      
      // Si l'erreur indique qu'une requête existe déjà ou qu'un match existe déjà,
      // on retire quand même le profil de la liste car l'action a été "traitée"
      if (
        errorMessage.includes('already exists') ||
        errorMessage.includes('déjà existant') ||
        errorMessage.includes('match already') ||
        errorMessage.includes('A match request already exists')
      ) {
        // Retirer le profil de la liste silencieusement
        setProfiles((prev) => prev.filter((p) => p.id !== userId));
        console.log('ℹ️ Action déjà effectuée, profil retiré de la liste');
        // Recharger les recommandations pour éviter ce problème à l'avenir
        setTimeout(() => {
          loadRecommendations();
        }, 500);
      } else {
        alert(errorMessage);
      }
    } finally {
      setSwiping(false);
    }
  };

  if (!user) {
    return null;
  }

  if (loading) {
    return (
      <section className="glass-card p-8">
        <div className="text-center">
          <Loader2 size={32} className="animate-spin mx-auto mb-4 text-[#8ad6ff]" />
          <p className="text-white/65">Chargement des recommandations...</p>
        </div>
      </section>
    );
  }

  if (profiles.length === 0) {
    return (
      <section className="glass-card p-8">
        <div className="text-center text-white/65">
          <p>Aucune recommandation disponible pour le moment</p>
        </div>
      </section>
    );
  }

  const displayedProfiles = profiles.slice(0, 3);
  const currentProfile = displayedProfiles[0];

  return (
    <section className="glass-card p-6 md:p-8">
      <div className="flex items-center justify-between mb-6">
        <div>
          <p className="text-sm font-semibold text-[#8ad6ff] mb-1">Deck immersif</p>
          <h2 className="text-2xl font-bold">Swipe scénarisé avec actions exclusives</h2>
        </div>
        <div className="pill-neon flex items-center gap-2">
          <Flame size={16} />
          {profiles.length} profils
        </div>
      </div>

      <div className="relative h-[500px] mb-6">
        {displayedProfiles.map((profile, index) => (
          <motion.article
            key={profile.id}
            className="absolute inset-0 glass-card overflow-hidden"
            custom={index}
            variants={cardVariants}
            initial="initial"
            animate="animate"
            whileHover={{ y: -6 }}
          >
            <div
              className="h-2/3 bg-cover bg-center relative"
              style={{
                backgroundImage: profile.avatarUrl
                  ? `url(http://localhost:8000${profile.avatarUrl})`
                  : `linear-gradient(135deg, rgba(255,107,139,0.25), rgba(90,214,255,0.18))`,
              }}
            >
              <div className="absolute top-4 right-4 pill-neon flex items-center gap-1">
                <Star size={14} />
                Score {profile.score}
              </div>
            </div>
            <div className="p-6 h-1/3 flex flex-col justify-between">
              <div>
                <h3 className="text-xl font-bold mb-1">
                  {profile.username || profile.email}, {profile.age || '?'}
                </h3>
                <p className="text-white/70 text-sm mb-2">{profile.bio || 'Pas de bio'}</p>
                <div className="flex items-center gap-4 text-xs text-white/50 mb-3">
                  <span>{profile.location || 'Localisation inconnue'}</span>
                  <span>Niveau {profile.level}</span>
                </div>
                <div className="flex flex-wrap gap-2">
                  {(profile.tags || []).slice(0, 3).map((tag) => (
                    <span key={tag} className="px-2 py-1 bg-white/5 rounded-full text-xs">
                      {tag}
                    </span>
                  ))}
                </div>
              </div>
            </div>
          </motion.article>
        ))}
      </div>

      <div className="flex items-center justify-center gap-4">
        <button
          className="btn-ghost flex items-center gap-2"
          onClick={() => currentProfile && handleAction('dislike', currentProfile.id)}
          disabled={swiping}
        >
          <X size={18} />
          Passer
        </button>
        <button
          className="btn-primary flex items-center gap-2"
          onClick={() => currentProfile && handleAction('like', currentProfile.id)}
          disabled={swiping}
        >
          <Heart size={18} />
          Matcher
        </button>
        <button
          className="btn-ghost flex items-center gap-2 border-[#ff7a84]/30 text-[#ff7a84]"
          onClick={() => currentProfile && handleAction('superlike', currentProfile.id)}
          disabled={swiping}
        >
          <Sparkles size={18} />
          Superlike
        </button>
      </div>
    </section>
  );
}

