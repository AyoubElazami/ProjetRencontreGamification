'use client';

import { useAuth } from '@/contexts/AuthContext';
import { LogOut, User, Trophy } from 'lucide-react';
import { useRouter } from 'next/navigation';
import Notifications from './Notifications';

export default function UserStatus() {
  const { user, logout } = useAuth();
  const router = useRouter();

  if (!user) {
    return null;
  }

  const handleLogout = () => {
    logout();
    router.push('/');
  };

  return (
    <div className="fixed top-4 right-4 z-50 glass-card p-4">
      <div className="flex items-center gap-4">
        <div>
          <p className="font-bold text-[#8ad6ff] mb-1">
            ✅ {user.username || user.email.split('@')[0]}
          </p>
          <p className="text-xs text-white/60">
            Niveau {user.level} • {user.xp} XP • Score {user.score}
          </p>
        </div>
        <div className="flex items-center gap-2">
          <button
            onClick={() => router.push('/leaderboard')}
            className="p-2 hover:bg-white/10 rounded-lg transition-colors"
            title="Classement"
          >
            <Trophy size={18} />
          </button>
          <button
            onClick={() => router.push('/profile')}
            className="p-2 hover:bg-white/10 rounded-lg transition-colors"
            title="Profil"
          >
            <User size={18} />
          </button>
          <Notifications />
          <button
            onClick={handleLogout}
            className="p-2 bg-[#ff7a84]/20 border border-[#ff7a84]/30 rounded-lg text-[#ff7a84] hover:bg-[#ff7a84]/30 transition-colors"
            title="Se déconnecter"
          >
            <LogOut size={18} />
          </button>
        </div>
      </div>
    </div>
  );
}

