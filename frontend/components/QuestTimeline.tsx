'use client';

import { useState, useEffect } from 'react';
import { gamificationAPI, Quest } from '@/lib/api';
import { useAuth } from '@/contexts/AuthContext';
import { Loader2 } from 'lucide-react';

export default function QuestTimeline() {
  const { user } = useAuth();
  const [quests, setQuests] = useState<Quest[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (user) {
      loadQuests();
    }
  }, [user]);

  const loadQuests = async () => {
    try {
      setLoading(true);
      const gamification = await gamificationAPI.getGamification();
      setQuests(gamification.quests || []);
    } catch (error) {
      console.error('Erreur lors du chargement des quêtes:', error);
    } finally {
      setLoading(false);
    }
  };

  if (!user) {
    return null;
  }

  return (
    <section className="glass-card p-6 md:p-8">
      <div className="flex items-center justify-between mb-6">
        <div>
          <p className="text-sm font-semibold text-[#8ad6ff] mb-1">Flow gamifié</p>
          <h2 className="text-2xl font-bold">La quête qui te fait matcher différemment</h2>
        </div>
        <span className="pill-ghost">Mode story-driven</span>
      </div>

      {loading ? (
        <div className="text-center py-12">
          <Loader2 size={32} className="animate-spin mx-auto mb-4 text-[#8ad6ff]" />
        </div>
      ) : quests.length === 0 ? (
        <div className="text-center py-12 text-white/65">
          <p>Aucune quête disponible</p>
        </div>
      ) : (
        <div className="space-y-4">
          {quests.map((quest, index) => (
            <article key={quest.code} className="glass-card p-4 flex items-start gap-4">
              <div
                className={`w-10 h-10 rounded-full flex items-center justify-center font-bold ${
                  quest.isCompleted
                    ? 'bg-green-500/20 text-green-400 border border-green-500/30'
                    : 'bg-white/5 text-white/70 border border-white/10'
                }`}
              >
                {quest.isCompleted ? '✓' : index + 1}
              </div>
              <div className="flex-1">
                <h3 className="font-bold mb-1">{quest.title}</h3>
                <p className="text-white/70 text-sm mb-2">{quest.description || 'Pas de description'}</p>
                <small className="text-white/50 text-xs">
                  {quest.isCompleted
                    ? `Complétée • +${quest.xpReward} XP`
                    : `Progression: ${JSON.stringify(quest.progress)} • +${quest.xpReward} XP`}
                </small>
              </div>
            </article>
          ))}
        </div>
      )}
    </section>
  );
}

