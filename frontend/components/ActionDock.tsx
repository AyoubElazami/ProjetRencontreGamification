'use client';

import { useState, useEffect } from 'react';
import { Gamepad2, HeartHandshake, Map, Rocket } from 'lucide-react';
import { useAuth } from '@/contexts/AuthContext';
import { matchAPI, gamificationAPI } from '@/lib/api';

export default function ActionDock() {
  const { user } = useAuth();
  const [matchesCount, setMatchesCount] = useState(0);
  const [questsCount, setQuestsCount] = useState(0);

  useEffect(() => {
    if (user) {
      loadStats();
    }
  }, [user]);

  const loadStats = async () => {
    try {
      const [matchesResponse, gamification] = await Promise.all([
        matchAPI.getMatches().catch(() => ({ matches: [] })),
        gamificationAPI.getGamification().catch(() => ({ quests: [] })),
      ]);
      setMatchesCount(matchesResponse.matches?.length || 0);
      setQuestsCount(gamification.quests?.filter((q: any) => !q.isCompleted).length || 0);
    } catch (error) {
      console.error('Erreur lors du chargement des stats:', error);
    }
  };

  const actions = [
    {
      icon: Rocket,
      label: 'Boost',
      detail: 'x2 visibilité',
      highlight: true,
      onClick: () => alert('Boost activé ! (Fonctionnalité à venir)'),
    },
    {
      icon: Gamepad2,
      label: 'Quêtes',
      detail: `${questsCount} nouvelle${questsCount > 1 ? 's' : ''}`,
      onClick: () => {
        const questSection = document.querySelector('.quest-timeline');
        if (questSection) {
          questSection.scrollIntoView({ behavior: 'smooth' });
        }
      },
    },
    {
      icon: HeartHandshake,
      label: 'Matches',
      detail: `${matchesCount} connecté${matchesCount > 1 ? 's' : ''}`,
      onClick: () => {
        const matchSection = document.querySelector('.match-grid');
        if (matchSection) {
          matchSection.scrollIntoView({ behavior: 'smooth' });
        }
      },
    },
    {
      icon: Map,
      label: 'Events',
      detail: 'Paris, Lyon',
      onClick: () => alert('Événements à venir !'),
    },
  ];

  if (!user) {
    return null;
  }

  return (
    <section className="glass-card p-4">
      <div className="flex items-center justify-around gap-2">
        {actions.map(({ icon: Icon, label, detail, highlight, onClick }) => (
          <button
            key={label}
            className={`flex-1 flex flex-col items-center gap-2 p-4 rounded-lg transition-all ${
              highlight
                ? 'bg-gradient-to-r from-[#8ad6ff]/20 to-[#ff7a84]/20 border border-[#8ad6ff]/30'
                : 'hover:bg-white/5'
            }`}
            onClick={onClick}
          >
            <Icon size={20} className={highlight ? 'text-[#8ad6ff]' : 'text-white/70'} />
            <div className="text-center">
              <span className="block text-sm font-semibold">{label}</span>
              <small className="text-xs text-white/60">{detail}</small>
            </div>
          </button>
        ))}
      </div>
    </section>
  );
}

