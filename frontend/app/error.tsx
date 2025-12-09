'use client';

import { useEffect } from 'react';
import { AlertCircle } from 'lucide-react';

export default function Error({
  error,
  reset,
}: {
  error: Error & { digest?: string };
  reset: () => void;
}) {
  useEffect(() => {
    console.error(error);
  }, [error]);

  return (
    <div className="min-h-screen flex items-center justify-center p-4">
      <div className="glass-card p-8 max-w-md w-full text-center">
        <AlertCircle size={48} className="mx-auto mb-4 text-[#ff7a84]" />
        <h2 className="text-2xl font-bold mb-4">Une erreur est survenue</h2>
        <p className="text-white/70 mb-6">{error.message || 'Erreur inconnue'}</p>
        <button
          onClick={reset}
          className="btn-primary"
        >
          Réessayer
        </button>
      </div>
    </div>
  );
}

