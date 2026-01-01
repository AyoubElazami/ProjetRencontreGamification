'use client';

import { useState, useEffect } from 'react';
import { gamificationAPI } from '@/lib/api';
import { useAuth } from '@/contexts/AuthContext';
import { Trophy, Medal, Award, Loader2 } from 'lucide-react';
import Navigation from '@/components/Navigation';
import ProtectedRoute from '@/components/ProtectedRoute';

export default function LeaderboardPage() {
  const { user } = useAuth();
  const [leaderboard, setLeaderboard] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadLeaderboard();
  }, []);

  const loadLeaderboard = async () => {
    try {
      setLoading(true);
      const response = await gamificationAPI.getLeaderboard(20);
      setLeaderboard(response.leaderboard || []);
    } catch (error) {
      console.error('Erreur lors du chargement du leaderboard:', error);
    } finally {
      setLoading(false);
    }
  };

  const getRankIcon = (rank: number) => {
    if (rank === 1) return <Trophy className="text-yellow-400" size={24} />;
    if (rank === 2) return <Medal className="text-gray-300" size={24} />;
    if (rank === 3) return <Award className="text-orange-400" size={24} />;
    return <span className="text-white/60 font-bold">#{rank}</span>;
  };

  return (
    <ProtectedRoute>
      <div className="min-h-screen p-4 md:p-8 pb-24">
      <div className="max-w-4xl mx-auto">
        <h1 className="text-3xl font-bold mb-8 bg-gradient-to-r from-[#8ad6ff] to-[#ff7a84] bg-clip-text text-transparent">
          Classement
        </h1>

        {loading ? (
          <div className="glass-card p-6 md:p-8">
            <div className="space-y-3">
              {[...Array(5)].map((_, index) => (
                <div key={index} className="glass-card p-4 flex items-center gap-4 animate-pulse">
                  <div className="w-12 h-12 bg-white/10 rounded-full"></div>
                  <div className="w-12 h-12 bg-white/10 rounded-full"></div>
                  <div className="flex-1">
                    <div className="h-5 w-32 bg-white/10 rounded mb-2"></div>
                    <div className="h-4 w-48 bg-white/5 rounded"></div>
                  </div>
                  <div className="text-right">
                    <div className="h-6 w-16 bg-white/10 rounded mb-1"></div>
                    <div className="h-4 w-12 bg-white/5 rounded"></div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        ) : (
          <div className="glass-card p-6 md:p-8">
            <div className="space-y-3">
              {leaderboard.map((entry) => {
                const isCurrentUser = user && entry.user.id === user.id;
                return (
                  <div
                    key={entry.rank}
                    className={`glass-card p-4 flex items-center gap-4 ${
                      isCurrentUser ? 'border-2 border-[#8ad6ff]/50 bg-[#8ad6ff]/10' : ''
                    }`}
                  >
                    <div className="w-12 flex items-center justify-center">
                      {getRankIcon(entry.rank)}
                    </div>
                    <div
                      className="w-12 h-12 rounded-full bg-cover bg-center"
                      style={{
                        backgroundImage: entry.user.avatarUrl
                          ? `url(http://localhost:8000${entry.user.avatarUrl})`
                          : `linear-gradient(135deg, rgba(255,107,139,0.5), rgba(90,214,255,0.5))`,
                      }}
                    />
                    <div className="flex-1">
                      <h3 className="font-bold">
                        {entry.user.username || `User #${entry.user.id}`}
                        {isCurrentUser && <span className="ml-2 text-[#8ad6ff]">(Vous)</span>}
                      </h3>
                      <p className="text-sm text-white/60">
                        Niveau {entry.user.level} • {entry.user.xp} XP
                      </p>
                    </div>
                    <div className="text-right">
                      <p className="text-xl font-bold text-[#8ad6ff]">{entry.user.score}</p>
                      <p className="text-xs text-white/60">Score</p>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        )}
      </div>
      <Navigation />
      </div>
    </ProtectedRoute>
  );
}

