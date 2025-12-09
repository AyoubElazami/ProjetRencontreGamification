'use client';

import { useState, useEffect } from 'react';
import { gamificationAPI, matchAPI } from '@/lib/api';
import { useAuth } from '@/contexts/AuthContext';
import { Loader2 } from 'lucide-react';

export default function ExperienceMetrics() {
  const { user } = useAuth();
  const [stats, setStats] = useState({
    matches: 0,
    messages: 0,
    quests: 0,
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (user) {
      loadStats();
    }
  }, [user]);

  const loadStats = async () => {
    try {
      setLoading(true);
      const [matchesResponse, gamification] = await Promise.all([
        matchAPI.getMatches().catch(() => ({ matches: [] })),
        gamificationAPI.getGamification().catch(() => ({ quests: [] })),
      ]);

      const matches = matchesResponse.matches || [];
      const completedQuests = gamification.quests?.filter((q: any) => q.isCompleted).length || 0;

      setStats({
        matches: matches.length,
        messages: 0,
        quests: completedQuests,
      });
    } catch (error) {
      console.error('Erreur lors du chargement des stats:', error);
    } finally {
      setLoading(false);
    }
  };

  if (!user) {
    return null;
  }

  const experiences = [
    { label: 'Niveau actuel', value: user.level.toString(), trend: `${user.xp} XP` },
    { label: 'Score total', value: user.score.toString(), trend: `#${Math.floor(user.score / 100)}` },
    { label: 'Quêtes complétées', value: stats.quests.toString(), trend: '+0%' },
  ];

  return (
    <section className="glass-card p-6 md:p-8">
      <div className="mb-6">
        <p className="text-sm font-semibold text-[#8ad6ff] mb-1">Impact communauté</p>
        <h2 className="text-2xl font-bold">Des chiffres qui parlent</h2>
      </div>

      {loading ? (
        <div className="text-center py-12">
          <Loader2 size={32} className="animate-spin mx-auto mb-4 text-[#8ad6ff]" />
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          {experiences.map((exp) => (
            <article key={exp.label} className="glass-card p-6 text-center">
              <p className="text-3xl font-bold mb-2">{exp.value}</p>
              <p className="text-white/60 mb-2">{exp.label}</p>
              <span className="text-[#8ad6ff] font-semibold">{exp.trend}</span>
            </article>
          ))}
        </div>
      )}
    </section>
  );
}

