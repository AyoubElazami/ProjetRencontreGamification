'use client';

import { useState, useEffect, useRef } from 'react';
import { useParams, useRouter } from 'next/navigation';
import { useAuth } from '@/contexts/AuthContext';
import { matchAPI, Match } from '@/lib/api';
import { Send, ArrowLeft, Loader2 } from 'lucide-react';
import Navigation from '@/components/Navigation';
import ProtectedRoute from '@/components/ProtectedRoute';

export default function MatchChatPage() {
  const params = useParams();
  const router = useRouter();
  const { user } = useAuth();
  const [match, setMatch] = useState<Match | null>(null);
  const [messages, setMessages] = useState<any[]>([]);
  const [message, setMessage] = useState('');
  const [loading, setLoading] = useState(true);
  const [sending, setSending] = useState(false);
  const messagesEndRef = useRef<HTMLDivElement>(null);

  const matchId = parseInt(params.id as string);

  useEffect(() => {
    if (user && matchId) {
      loadMatch();
      loadMessages();
    }
  }, [user, matchId]);

  useEffect(() => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [messages]);

  const loadMatch = async () => {
    try {
      const matchData = await matchAPI.getMatch(matchId);
      setMatch(matchData);
    } catch (error) {
      console.error('Erreur lors du chargement du match:', error);
    }
  };

  const loadMessages = async () => {
    try {
      setLoading(true);
      const response = await matchAPI.getMessages(matchId);
      setMessages(response.messages || []);
    } catch (error) {
      console.error('Erreur lors du chargement des messages:', error);
    } finally {
      setLoading(false);
    }
  };

  const sendMessage = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!message.trim() || sending) return;

    setSending(true);
    try {
      await matchAPI.sendMessage(matchId, message);
      setMessage('');
      await loadMessages();
    } catch (error) {
      console.error('Erreur lors de l\'envoi du message:', error);
      alert('Erreur lors de l\'envoi du message');
    } finally {
      setSending(false);
    }
  };

  if (!user || !match) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <Loader2 size={32} className="animate-spin text-[#8ad6ff]" />
      </div>
    );
  }

  return (
    <ProtectedRoute>
      <div className="min-h-screen flex flex-col pb-20">
      <div className="glass-card border-b border-white/10 p-4 flex items-center gap-4">
        <button
          onClick={() => router.back()}
          className="p-2 hover:bg-white/10 rounded-lg transition-colors"
        >
          <ArrowLeft size={20} />
        </button>
        <div
          className="w-12 h-12 rounded-full bg-cover bg-center"
          style={{
            backgroundImage: match.user.avatarUrl
              ? `url(http://localhost:8000${match.user.avatarUrl})`
              : `linear-gradient(135deg, rgba(255,107,139,0.5), rgba(90,214,255,0.5))`,
          }}
        />
        <div>
          <h2 className="font-bold">{match.user.username || match.user.email}</h2>
          <p className="text-sm text-white/60">{match.user.bio || 'Pas de bio'}</p>
        </div>
      </div>

      <div className="flex-1 overflow-y-auto p-4 space-y-4">
        {loading ? (
          <div className="flex items-center justify-center h-full">
            <Loader2 size={32} className="animate-spin text-[#8ad6ff]" />
          </div>
        ) : messages.length === 0 ? (
          <div className="text-center text-white/60 py-12">
            <p>Aucun message pour le moment. Envoyez le premier message !</p>
          </div>
        ) : (
          messages.map((msg) => {
            const isMe = msg.senderId === user.id;
            return (
              <div
                key={msg.id}
                className={`flex ${isMe ? 'justify-end' : 'justify-start'}`}
              >
                <div
                  className={`max-w-[70%] p-3 rounded-lg ${
                    isMe
                      ? 'bg-gradient-to-r from-[#8ad6ff] to-[#ff7a84] text-white'
                      : 'bg-white/10 text-white'
                  }`}
                >
                  <p>{msg.content}</p>
                  <p className="text-xs mt-1 opacity-70">
                    {new Date(msg.createdAt).toLocaleTimeString('fr-FR', {
                      hour: '2-digit',
                      minute: '2-digit',
                    })}
                  </p>
                </div>
              </div>
            );
          })
        )}
        <div ref={messagesEndRef} />
      </div>

      <form onSubmit={sendMessage} className="glass-card border-t border-white/10 p-4">
        <div className="flex items-center gap-2">
          <input
            type="text"
            value={message}
            onChange={(e) => setMessage(e.target.value)}
            placeholder="Tapez votre message..."
            className="flex-1 px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white placeholder-white/40 focus:outline-none focus:border-[#8ad6ff]/50"
            disabled={sending}
          />
          <button
            type="submit"
            disabled={!message.trim() || sending}
            className="btn-primary p-2 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {sending ? (
              <Loader2 size={20} className="animate-spin" />
            ) : (
              <Send size={20} />
            )}
          </button>
        </div>
      </form>
      <Navigation />
      </div>
    </ProtectedRoute>
  );
}

