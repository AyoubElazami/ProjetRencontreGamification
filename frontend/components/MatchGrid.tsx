'use client';

import { useState, useEffect } from 'react';
import { matchAPI, Match } from '@/lib/api';
import { useAuth } from '@/contexts/AuthContext';
import { Loader2, MessageCircle } from 'lucide-react';
import { useRouter } from 'next/navigation';

export default function MatchGrid() {
  const { user } = useAuth();
  const router = useRouter();
  const [matches, setMatches] = useState<Match[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (user) {
      loadMatches();
    }
  }, [user]);

  useEffect(() => {
    const handleMatchCreated = () => {
      loadMatches();
    };

    window.addEventListener('match-created', handleMatchCreated);
    return () => {
      window.removeEventListener('match-created', handleMatchCreated);
    };
  }, []);

  const loadMatches = async () => {
    try {
      setLoading(true);
      const response = await matchAPI.getMatches();
      setMatches(response.matches || []);
    } catch (error: any) {
      console.error('Erreur lors du chargement des matches:', error);
      setMatches([]);
    } finally {
      setLoading(false);
    }
  };

  if (!user) {
    return null;
  }

  return (
    <section className="glass-card p-6 md:p-8">
      <div className="mb-6">
        <p className="text-sm font-semibold text-[#8ad6ff] mb-1">Expérience</p>
        <h2 className="text-2xl font-bold">
          {loading ? 'Chargement...' : `Vous avez ${matches.length} match${matches.length > 1 ? 'es' : ''}`}
        </h2>
      </div>

      {loading ? (
        <div className="text-center py-12">
          <Loader2 size={32} className="animate-spin mx-auto mb-4 text-[#8ad6ff]" />
        </div>
      ) : matches.length > 0 ? (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="matches">
          {matches.map((match) => (
            <article
              key={match.id}
              className="glass-card p-6 bg-gradient-to-br from-[#8ad6ff]/10 to-[#ff7a84]/10 border-[#8ad6ff]/20 hover:border-[#8ad6ff]/40 transition-colors cursor-pointer"
              onClick={() => router.push(`/matches/${match.id}`)}
            >
              <div className="flex items-center gap-4 mb-4">
                <div
                  className="w-16 h-16 rounded-full bg-cover bg-center"
                  style={{
                    backgroundImage: match.user.avatarUrl
                      ? `url(http://localhost:8000${match.user.avatarUrl})`
                      : `linear-gradient(135deg, rgba(255,107,139,0.5), rgba(90,214,255,0.5))`,
                  }}
                />
                <div>
                  <h3 className="font-bold text-lg">{match.user.username || match.user.email}</h3>
                  <p className="text-white/60 text-sm">{match.user.bio || 'Pas de bio'}</p>
                </div>
              </div>
              <button
                className="w-full btn-ghost flex items-center justify-center gap-2"
                onClick={(e) => {
                  e.stopPropagation();
                  router.push(`/matches/${match.id}`);
                }}
              >
                <MessageCircle size={18} />
                Voir le match
              </button>
            </article>
          ))}
        </div>
      ) : (
        <div className="text-center py-12 text-white/65">
          <p>Aucun match pour le moment. Continuez à swiper pour trouver des matches !</p>
        </div>
      )}
    </section>
  );
}

