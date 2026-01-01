'use client';

import { usePathname, useRouter } from 'next/navigation';
import Link from 'next/link';
import { useEffect, useState } from 'react';
import { Home, MessageCircle, User, Trophy, Heart, Bell } from 'lucide-react';
import { useAuth } from '@/contexts/AuthContext';
import { matchAPI, notificationAPI } from '@/lib/api';

export default function Navigation() {
  const pathname = usePathname();
  const router = useRouter();
  const { user } = useAuth();
  const [unreadLikesCount, setUnreadLikesCount] = useState(0);
  const [unreadNotificationsCount, setUnreadNotificationsCount] = useState(0);

  useEffect(() => {
    if (user) {
      loadCounts();
      // Rafraîchir toutes les 30 secondes
      const interval = setInterval(loadCounts, 30000);
      return () => clearInterval(interval);
    }
  }, [user]);

  const loadCounts = async () => {
    try {
      // Compter les likes reçus non traités
      const likesData = await matchAPI.getReceivedLikes();
      setUnreadLikesCount(likesData.likes.length);

      // Compter les notifications non lues
      const notificationsData = await notificationAPI.getNotifications(50, 0);
      setUnreadNotificationsCount(notificationsData.unreadCount);
    } catch (error) {
      console.error('Erreur lors du chargement des compteurs:', error);
    }
  };

  if (!user) {
    return null;
  }

  const navItems = [
    { icon: Home, label: 'Accueil', path: '/dashboard' },
    { icon: Heart, label: 'Matches', path: '/dashboard', scrollTo: 'matches' },
    { 
      icon: Bell, 
      label: 'Likes', 
      path: '/likes',
      badge: unreadLikesCount > 0 ? unreadLikesCount : undefined
    },
    { icon: Trophy, label: 'Classement', path: '/leaderboard' },
    { icon: User, label: 'Profil', path: '/profile' },
  ];

  const handleScrollTo = (scrollTo: string) => {
    if (pathname === '/dashboard') {
      setTimeout(() => {
        const element = document.querySelector(`.${scrollTo}`);
        if (element) {
          element.scrollIntoView({ behavior: 'smooth' });
        }
      }, 100);
    }
  };

  return (
    <nav className="fixed bottom-0 left-0 right-0 z-50 glass-card border-t border-white/10 p-2">
      <div className="flex items-center justify-around max-w-2xl mx-auto">
        {navItems.map(({ icon: Icon, label, path, scrollTo, badge }, index) => {
          const isActive = pathname === path || (path === '/dashboard' && pathname.startsWith('/dashboard'));
          
          // Pour les liens avec scrollTo, utiliser un bouton
          if (scrollTo) {
            return (
              <button
                key={`${label}-${index}`}
                onClick={() => handleScrollTo(scrollTo)}
                className={`relative flex flex-col items-center gap-1 p-2 rounded-lg transition-all ${
                  isActive
                    ? 'text-[#8ad6ff] bg-[#8ad6ff]/10'
                    : 'text-white/60 hover:text-white/80 hover:bg-white/5'
                }`}
              >
                <Icon size={20} />
                <span className="text-xs">{label}</span>
                {badge && badge > 0 && (
                  <span className="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                    {badge > 9 ? '9+' : badge}
                  </span>
                )}
              </button>
            );
          }

          // Pour les liens normaux, utiliser Link pour le préchargement
          return (
            <Link
              key={`${label}-${index}`}
              href={path}
              className={`relative flex flex-col items-center gap-1 p-2 rounded-lg transition-all ${
                isActive
                  ? 'text-[#8ad6ff] bg-[#8ad6ff]/10'
                  : 'text-white/60 hover:text-white/80 hover:bg-white/5'
              }`}
            >
              <Icon size={20} />
              <span className="text-xs">{label}</span>
              {badge && badge > 0 && (
                <span className="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                  {badge > 9 ? '9+' : badge}
                </span>
              )}
            </Link>
          );
        })}
      </div>
    </nav>
  );
}

