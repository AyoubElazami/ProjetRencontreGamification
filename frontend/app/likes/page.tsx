'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import { matchAPI, userAPI, type User } from '@/lib/api';
import { useAuth } from '@/contexts/AuthContext';
import ProtectedRoute from '@/components/ProtectedRoute';
import { Heart, X, Star, ArrowLeft } from 'lucide-react';

interface ReceivedLike {
  id: number;
  type: 'like' | 'superlike';
  user: User;
  createdAt: string;
}

export default function LikesPage() {
  const router = useRouter();
  const { user } = useAuth();
  const [likes, setLikes] = useState<ReceivedLike[]>([]);
  const [loading, setLoading] = useState(true);
  const [processing, setProcessing] = useState<number | null>(null);

  useEffect(() => {
    loadLikes();
  }, []);

  const loadLikes = async () => {
    try {
      setLoading(true);
      const data = await matchAPI.getReceivedLikes();
      setLikes(data.likes);
    } catch (error) {
      console.error('Erreur lors du chargement des likes:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleLike = async (likedUser: User, likeId: number) => {
    if (processing) return;
    
    try {
      setProcessing(likeId);
      const result = await matchAPI.like(likedUser.id);
      
      // Si c'est un match, afficher une alerte
      if (result.matchRequest?.status === 'matched') {
        alert('🎉 Match ! Vous avez un nouveau match !');
        // Recharger les likes et rediriger vers les matches
        await loadLikes();
        router.push('/dashboard');
      } else {
        // Retirer le like de la liste
        setLikes(likes.filter(like => like.id !== likeId));
      }
    } catch (error: any) {
      console.error('Erreur lors du like:', error);
      const errorMessage = error.message || 'Erreur lors du like';
      
      // Si c'est une erreur "already exists", c'est probablement qu'un match a été créé
      // ou que la requête existe déjà - recharger les likes pour vérifier
      if (errorMessage.includes('already exists')) {
        await loadLikes();
        // Si le like n'est plus dans la liste, c'est qu'un match a été créé
        const stillExists = likes.find(like => like.id === likeId);
        if (!stillExists) {
          alert('🎉 Match ! Vous avez un nouveau match !');
          router.push('/dashboard');
        } else {
          alert('Cette action a déjà été effectuée.');
        }
      } else {
        alert(errorMessage);
      }
    } finally {
      setProcessing(null);
    }
  };

  const handleDislike = async (likedUser: User, likeId: number) => {
    if (processing) return;
    
    try {
      setProcessing(likeId);
      await matchAPI.dislike(likedUser.id);
      // Retirer le like de la liste
      setLikes(likes.filter(like => like.id !== likeId));
    } catch (error: any) {
      console.error('Erreur lors du dislike:', error);
      alert(error.message || 'Erreur lors du dislike');
    } finally {
      setProcessing(null);
    }
  };

  if (loading) {
    return (
      <ProtectedRoute>
        <div className="min-h-screen bg-gradient-to-br from-[#0a0e27] via-[#1a1f3a] to-[#2d1b4e] flex items-center justify-center">
          <div className="text-white text-xl">Chargement...</div>
        </div>
      </ProtectedRoute>
    );
  }

  return (
    <ProtectedRoute>
      <div className="min-h-screen bg-gradient-to-br from-[#0a0e27] via-[#1a1f3a] to-[#2d1b4e] pb-20">
        <div className="container mx-auto px-4 py-6">
          {/* Header */}
          <div className="flex items-center gap-4 mb-6">
            <button
              onClick={() => router.back()}
              className="p-2 rounded-lg glass-card hover:bg-white/10 transition-colors"
            >
              <ArrowLeft size={24} className="text-white" />
            </button>
            <h1 className="text-3xl font-bold text-white">
              Likes reçus
            </h1>
            {likes.length > 0 && (
              <span className="ml-auto px-3 py-1 rounded-full bg-[#8ad6ff]/20 text-[#8ad6ff] text-sm font-semibold">
                {likes.length}
              </span>
            )}
          </div>

          {likes.length === 0 ? (
            <div className="text-center py-20">
              <Heart size={64} className="mx-auto text-white/20 mb-4" />
              <p className="text-white/60 text-lg">
                Aucun like reçu pour le moment
              </p>
              <p className="text-white/40 text-sm mt-2">
                Continuez à swiper pour recevoir des likes !
              </p>
            </div>
          ) : (
            <div className="space-y-4">
              {likes.map((like) => (
                <div
                  key={like.id}
                  className="glass-card p-6 rounded-xl hover:bg-white/5 transition-all"
                >
                  <div className="flex items-start gap-4">
                    {/* Avatar */}
                    <div className="relative">
                      <div
                        className="w-20 h-20 rounded-full bg-cover bg-center border-2 border-[#8ad6ff]/30"
                        style={{
                          backgroundImage: like.user.avatarUrl
                            ? `url(http://localhost:8000${like.user.avatarUrl})`
                            : `linear-gradient(135deg, rgba(255,107,139,0.5), rgba(90,214,255,0.5))`,
                        }}
                      />
                      {like.type === 'superlike' && (
                        <div className="absolute -top-2 -right-2 bg-yellow-500 rounded-full p-1">
                          <Star size={16} className="text-white fill-white" />
                        </div>
                      )}
                    </div>

                    {/* User Info */}
                    <div className="flex-1">
                      <div className="flex items-center gap-2 mb-1">
                        <h3 className="text-xl font-semibold text-white">
                          {like.user.username || 'Utilisateur'}
                        </h3>
                        {like.user.age && (
                          <span className="text-white/60 text-sm">
                            {like.user.age} ans
                          </span>
                        )}
                        {like.user.level && (
                          <span className="px-2 py-0.5 rounded-full bg-[#8ad6ff]/20 text-[#8ad6ff] text-xs font-semibold">
                            Niveau {like.user.level}
                          </span>
                        )}
                      </div>

                      {like.user.bio && (
                        <p className="text-white/70 text-sm mb-2 line-clamp-2">
                          {like.user.bio}
                        </p>
                      )}

                      {like.user.location && (
                        <p className="text-white/50 text-xs mb-3">
                          📍 {like.user.location}
                        </p>
                      )}

                      {like.user.tags && like.user.tags.length > 0 && (
                        <div className="flex flex-wrap gap-2 mb-3">
                          {like.user.tags.slice(0, 3).map((tag, idx) => (
                            <span
                              key={idx}
                              className="px-2 py-1 rounded-full bg-white/10 text-white/70 text-xs"
                            >
                              {tag}
                            </span>
                          ))}
                        </div>
                      )}

                      <p className="text-white/40 text-xs mb-4">
                        {like.type === 'superlike' ? '⭐ Vous a superliké' : '❤️ Vous a liké'} •{' '}
                        {new Date(like.createdAt).toLocaleDateString('fr-FR', {
                          day: 'numeric',
                          month: 'long',
                          hour: '2-digit',
                          minute: '2-digit',
                        })}
                      </p>

                      {/* Actions */}
                      <div className="flex gap-3">
                        <button
                          onClick={() => handleDislike(like.user, like.id)}
                          disabled={processing === like.id}
                          className="flex-1 px-4 py-2 rounded-lg bg-white/10 hover:bg-red-500/20 text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                          <X size={20} />
                          <span>Passer</span>
                        </button>
                        <button
                          onClick={() => handleLike(like.user, like.id)}
                          disabled={processing === like.id}
                          className="flex-1 px-4 py-2 rounded-lg bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 text-white font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                          {processing === like.id ? (
                            <span>...</span>
                          ) : (
                            <>
                              <Heart size={20} className="fill-white" />
                              <span>Matcher</span>
                            </>
                          )}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
    </ProtectedRoute>
  );
}

