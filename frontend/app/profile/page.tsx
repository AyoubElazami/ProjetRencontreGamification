'use client';

import { useState, useEffect } from 'react';
import { useAuth } from '@/contexts/AuthContext';
import { useRouter } from 'next/navigation';
import { userAPI, User, gamificationAPI } from '@/lib/api';
import { Save, Upload, Loader2 } from 'lucide-react';
import Navigation from '@/components/Navigation';
import ProtectedRoute from '@/components/ProtectedRoute';
import BadgeDisplay from '@/components/BadgeDisplay';

export default function ProfilePage() {
  const { user, refreshUser } = useAuth();
  const router = useRouter();
  const [loading, setLoading] = useState(false);
  const [saving, setSaving] = useState(false);
  const [badges, setBadges] = useState<any[]>([]);
  const [formData, setFormData] = useState({
    username: '',
    bio: '',
    age: '',
    location: '',
    gender: '',
  });

  useEffect(() => {
    if (user) {
      setFormData({
        username: user.username || '',
        bio: user.bio || '',
        age: user.age?.toString() || '',
        location: user.location || '',
        gender: user.gender || '',
      });
      loadBadges();
    }
  }, [user]);

  const loadBadges = async () => {
    try {
      const gamification = await gamificationAPI.getGamification();
      setBadges(gamification.badges || []);
    } catch (error) {
      console.error('Erreur lors du chargement des badges:', error);
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);

    try {
      const updateData: Partial<User> = {
        username: formData.username || null,
        bio: formData.bio || null,
        age: formData.age ? parseInt(formData.age) : null,
        location: formData.location || null,
        gender: formData.gender || null,
      };

      await userAPI.updateMe(updateData);
      await refreshUser();
      alert('Profil mis à jour avec succès !');
    } catch (error: any) {
      console.error('Erreur lors de la mise à jour:', error);
      alert(error.response?.data?.error || 'Erreur lors de la mise à jour du profil');
    } finally {
      setSaving(false);
    }
  };

  const handleAvatarUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    setLoading(true);
    try {
      await userAPI.uploadAvatar(file);
      await refreshUser();
      alert('Avatar mis à jour avec succès !');
    } catch (error: any) {
      console.error('Erreur lors de l\'upload:', error);
      alert(error.response?.data?.error || 'Erreur lors de l\'upload de l\'avatar');
    } finally {
      setLoading(false);
    }
  };

  if (!user) {
    return (
      <ProtectedRoute>
        <div className="min-h-screen flex items-center justify-center">
          <Loader2 size={32} className="animate-spin text-[#8ad6ff]" />
        </div>
      </ProtectedRoute>
    );
  }

  return (
    <ProtectedRoute>
      <div className="min-h-screen p-4 md:p-8 pb-24">
      <div className="max-w-4xl mx-auto">
        <h1 className="text-3xl font-bold mb-8 bg-gradient-to-r from-[#8ad6ff] to-[#ff7a84] bg-clip-text text-transparent">
          Mon Profil
        </h1>

        <div className="glass-card p-6 md:p-8">
          <div className="flex flex-col md:flex-row items-center gap-6 mb-8">
            <div className="relative">
              <div
                className="w-32 h-32 rounded-full bg-cover bg-center border-4 border-[#8ad6ff]/30"
                style={{
                  backgroundImage: user?.avatarUrl
                    ? `url(http://localhost:8000${user.avatarUrl})`
                    : `linear-gradient(135deg, rgba(255,107,139,0.5), rgba(90,214,255,0.5))`,
                }}
              />
              <label className="absolute bottom-0 right-0 p-2 bg-[#8ad6ff] rounded-full cursor-pointer hover:bg-[#7ac5e6] transition-colors">
                <Upload size={18} className="text-white" />
                <input
                  type="file"
                  accept="image/*"
                  onChange={handleAvatarUpload}
                  className="hidden"
                  disabled={loading}
                />
              </label>
            </div>
            <div className="text-center md:text-left">
              <h2 className="text-2xl font-bold mb-2">{user?.username || user?.email}</h2>
              <p className="text-white/60 mb-2">{user?.email}</p>
              <div className="flex items-center gap-4 text-sm">
                <span>Niveau {user?.level || 0}</span>
                <span>{user?.xp || 0} XP</span>
                <span>Score {user?.score || 0}</span>
              </div>
            </div>
          </div>

          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label className="block text-sm font-semibold mb-2">Nom d'utilisateur</label>
              <input
                type="text"
                value={formData.username}
                onChange={(e) => setFormData({ ...formData, username: e.target.value })}
                className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#8ad6ff]/50"
              />
            </div>

            <div>
              <label className="block text-sm font-semibold mb-2">Bio</label>
              <textarea
                value={formData.bio}
                onChange={(e) => setFormData({ ...formData, bio: e.target.value })}
                rows={4}
                className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#8ad6ff]/50 resize-none"
              />
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-semibold mb-2">Âge</label>
                <input
                  type="number"
                  value={formData.age}
                  onChange={(e) => setFormData({ ...formData, age: e.target.value })}
                  min="18"
                  max="100"
                  className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#8ad6ff]/50"
                />
              </div>

              <div>
                <label className="block text-sm font-semibold mb-2">Genre</label>
                <select
                  value={formData.gender}
                  onChange={(e) => setFormData({ ...formData, gender: e.target.value })}
                  className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#8ad6ff]/50"
                >
                  <option value="">Sélectionner</option>
                  <option value="male">Homme</option>
                  <option value="female">Femme</option>
                  <option value="other">Autre</option>
                </select>
              </div>
            </div>

            <div>
              <label className="block text-sm font-semibold mb-2">Localisation</label>
              <input
                type="text"
                value={formData.location}
                onChange={(e) => setFormData({ ...formData, location: e.target.value })}
                placeholder="Paris, France"
                className="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-[#8ad6ff]/50"
              />
            </div>

            <div className="flex items-center gap-4 pt-4">
              <button
                type="submit"
                disabled={saving}
                className="btn-primary flex items-center gap-2 disabled:opacity-50"
              >
                {saving ? (
                  <>
                    <Loader2 size={18} className="animate-spin" />
                    Enregistrement...
                  </>
                ) : (
                  <>
                    <Save size={18} />
                    Enregistrer
                  </>
                )}
              </button>
              <button
                type="button"
                onClick={() => router.back()}
                className="btn-ghost"
              >
                Annuler
              </button>
            </div>
          </form>

          <div className="mt-8">
            <h3 className="text-xl font-bold mb-4">Mes Badges</h3>
            <BadgeDisplay badges={badges} />
          </div>
        </div>
      </div>
      <Navigation />
      </div>
    </ProtectedRoute>
  );
}

