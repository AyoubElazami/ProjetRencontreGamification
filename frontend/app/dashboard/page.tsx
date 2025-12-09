'use client';

import { useAuth } from '@/contexts/AuthContext';
import SwipeDeck from '@/components/SwipeDeck';
import MatchGrid from '@/components/MatchGrid';
import UserStatus from '@/components/UserStatus';
import ExperienceMetrics from '@/components/ExperienceMetrics';
import QuestTimeline from '@/components/QuestTimeline';
import ActionDock from '@/components/ActionDock';
import Navigation from '@/components/Navigation';
import ProtectedRoute from '@/components/ProtectedRoute';

export default function DashboardPage() {
  const { user } = useAuth();

  return (
    <ProtectedRoute>
      <div className="min-h-screen p-4 md:p-8 pb-24">
        <UserStatus />
        <div className="max-w-7xl mx-auto space-y-8">
          <div className="text-center mb-8">
            <h1 className="text-4xl font-bold mb-2 bg-gradient-to-r from-[#8ad6ff] to-[#ff7a84] bg-clip-text text-transparent">
              Bienvenue {user?.username || user?.email.split('@')[0]} !
            </h1>
            <p className="text-white/65">Découvrez de nouvelles personnes et créez des connexions</p>
          </div>

          <ActionDock />
          <SwipeDeck />
          <MatchGrid />
          <div className="grid md:grid-cols-2 gap-8">
            <ExperienceMetrics />
            <QuestTimeline />
          </div>
        </div>
        <Navigation />
      </div>
    </ProtectedRoute>
  );
}

