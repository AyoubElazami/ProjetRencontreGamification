'use client';

import { Badge } from '@/lib/api';
import { Award } from 'lucide-react';

export default function BadgeDisplay({ badges }: { badges: Badge[] }) {
  if (badges.length === 0) {
    return (
      <div className="text-center py-8 text-white/60">
        <p>Aucun badge obtenu pour le moment</p>
      </div>
    );
  }

  return (
    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
      {badges.map((badge) => (
        <div
          key={badge.code}
          className="glass-card p-4 text-center hover:border-[#8ad6ff]/50 transition-colors"
        >
          <div className="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-[#8ad6ff]/20 to-[#ff7a84]/20 rounded-full flex items-center justify-center">
            {badge.icon ? (
              <span className="text-2xl">{badge.icon}</span>
            ) : (
              <Award size={32} className="text-[#8ad6ff]" />
            )}
          </div>
          <h3 className="font-bold text-sm mb-1">{badge.name}</h3>
          {badge.description && (
            <p className="text-xs text-white/60 mb-2">{badge.description}</p>
          )}
          {badge.awardedAt && (
            <p className="text-xs text-white/40">
              {new Date(badge.awardedAt).toLocaleDateString('fr-FR')}
            </p>
          )}
        </div>
      ))}
    </div>
  );
}

