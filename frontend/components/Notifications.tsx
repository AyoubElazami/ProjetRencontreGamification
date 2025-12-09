'use client';

import { useState, useEffect } from 'react';
import { notificationAPI, Notification } from '@/lib/api';
import { useAuth } from '@/contexts/AuthContext';
import { Bell, Check, Loader2, X } from 'lucide-react';

export default function Notifications() {
  const { user } = useAuth();
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const [unreadCount, setUnreadCount] = useState(0);
  const [loading, setLoading] = useState(true);
  const [open, setOpen] = useState(false);

  useEffect(() => {
    if (user) {
      loadNotifications();
      const interval = setInterval(loadNotifications, 30000); // Refresh every 30s
      return () => clearInterval(interval);
    }
  }, [user]);

  const loadNotifications = async () => {
    try {
      const response = await notificationAPI.getNotifications(20, 0);
      setNotifications(response.notifications || []);
      setUnreadCount(response.unreadCount || 0);
    } catch (error) {
      console.error('Erreur lors du chargement des notifications:', error);
    } finally {
      setLoading(false);
    }
  };

  const markAsRead = async (id: number) => {
    try {
      await notificationAPI.markAsRead(id);
      await loadNotifications();
    } catch (error) {
      console.error('Erreur lors de la marque comme lu:', error);
    }
  };

  const markAllAsRead = async () => {
    try {
      await notificationAPI.markAllAsRead();
      await loadNotifications();
    } catch (error) {
      console.error('Erreur lors de la marque de tout comme lu:', error);
    }
  };

  if (!user) {
    return null;
  }

  return (
    <div className="relative">
      <button
        onClick={() => setOpen(!open)}
        className="relative p-2 hover:bg-white/10 rounded-lg transition-colors"
      >
        <Bell size={20} />
        {unreadCount > 0 && (
          <span className="absolute -top-1 -right-1 w-5 h-5 bg-[#ff7a84] rounded-full text-xs flex items-center justify-center">
            {unreadCount > 9 ? '9+' : unreadCount}
          </span>
        )}
      </button>

      {open && (
        <div className="absolute right-0 top-12 w-80 glass-card p-4 max-h-96 overflow-y-auto z-50">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-bold">Notifications</h3>
            <div className="flex items-center gap-2">
              {unreadCount > 0 && (
                <button
                  onClick={markAllAsRead}
                  className="text-xs text-[#8ad6ff] hover:underline"
                >
                  Tout marquer comme lu
                </button>
              )}
              <button
                onClick={() => setOpen(false)}
                className="p-1 hover:bg-white/10 rounded"
              >
                <X size={16} />
              </button>
            </div>
          </div>

          {loading ? (
            <div className="text-center py-8">
              <Loader2 size={24} className="animate-spin mx-auto mb-2 text-[#8ad6ff]" />
            </div>
          ) : notifications.length === 0 ? (
            <div className="text-center py-8 text-white/60 text-sm">
              Aucune notification
            </div>
          ) : (
            <div className="space-y-2">
              {notifications.map((notif) => (
                <div
                  key={notif.id}
                  className={`p-3 rounded-lg ${
                    !notif.isRead ? 'bg-[#8ad6ff]/10 border border-[#8ad6ff]/30' : 'bg-white/5'
                  }`}
                >
                  <div className="flex items-start justify-between gap-2">
                    <div className="flex-1">
                      <p className="text-sm font-semibold mb-1">{notif.type}</p>
                      <p className="text-xs text-white/70">
                        {new Date(notif.createdAt).toLocaleString('fr-FR')}
                      </p>
                    </div>
                    {!notif.isRead && (
                      <button
                        onClick={() => markAsRead(notif.id)}
                        className="p-1 hover:bg-white/10 rounded"
                        title="Marquer comme lu"
                      >
                        <Check size={16} />
                      </button>
                    )}
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      )}
    </div>
  );
}

