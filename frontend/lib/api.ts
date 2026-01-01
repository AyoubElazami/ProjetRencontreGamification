import axios from 'axios';

const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Intercepteur pour ajouter le token JWT
api.interceptors.request.use((config) => {
  if (typeof window !== 'undefined') {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
  }
  
  // Si c'est un FormData, supprimer le Content-Type pour qu'Axios le définisse automatiquement avec le boundary
  if (config.data instanceof FormData) {
    delete config.headers['Content-Type'];
  }
  
  return config;
});

// Types
export interface User {
  id: number;
  email: string;
  username: string | null;
  bio: string | null;
  gender: string | null;
  age: number | null;
  location: string | null;
  latitude: string | null;
  longitude: string | null;
  avatarUrl: string | null;
  level: number;
  xp: number;
  score: number;
  tags: string[];
  preferences: {
    age_range?: [number, number];
    distance_km?: number;
    gender_pref?: string;
  } | null;
}

export interface LoginResponse {
  token: string;
  user: {
    id: number;
    email: string;
    username: string | null;
  };
}

export interface Match {
  id: number;
  user: {
    id: number;
    email: string;
    username: string | null;
    avatarUrl: string | null;
    bio: string | null;
  };
  createdAt: string;
  lastInteractionAt: string | null;
}

export interface Notification {
  id: number;
  type: string;
  payload: any;
  readAt: string | null;
  createdAt: string;
  isRead: boolean;
}

export interface Badge {
  code: string;
  name: string;
  description: string | null;
  icon: string | null;
  awardedAt: string | null;
}

export interface Quest {
  code: string;
  title: string;
  description: string | null;
  xpReward: number;
  progress: any;
  completedAt: string | null;
  isCompleted: boolean;
}

export interface GamificationData {
  level: number;
  xp: number;
  score: number;
  badges: Badge[];
  quests: Quest[];
}

// API Auth
export const authAPI = {
  login: async (email: string, password: string): Promise<LoginResponse> => {
    const response = await api.post<LoginResponse>('/api/login', { email, password });
    return response.data;
  },

  register: async (email: string, password: string, username?: string): Promise<LoginResponse> => {
    const response = await api.post<LoginResponse>('/api/register', { email, password, username });
    return response.data;
  },
};

// API User
export const userAPI = {
  getMe: async (): Promise<User> => {
    const response = await api.get<User>('/api/me');
    return response.data;
  },

  updateMe: async (data: Partial<User>): Promise<User> => {
    const response = await api.put<User>('/api/me', data);
    return response.data;
  },

  uploadAvatar: async (file: File): Promise<{ avatarUrl: string }> => {
    const formData = new FormData();
    formData.append('avatar', file);
    // L'intercepteur supprimera automatiquement le Content-Type pour FormData
    const response = await api.post<{ avatarUrl: string }>('/api/me/avatar', formData);
    return response.data;
  },

  getUsers: async (page: number = 1, limit: number = 20): Promise<{ users: User[]; page: number; limit: number }> => {
    const response = await api.get<{ users: User[]; page: number; limit: number }>(
      `/api/users?page=${page}&limit=${limit}`
    );
    return response.data;
  },

  getUser: async (id: number): Promise<User> => {
    const response = await api.get<User>(`/api/users/${id}`);
    return response.data;
  },

  getRecommendations: async (limit: number = 20): Promise<{ users: User[] }> => {
    const response = await api.get<{ users: User[] }>(`/api/recommendations?limit=${limit}`);
    return response.data;
  },
};

// API Matchmaking
export const matchAPI = {
  like: async (userId: number): Promise<{ success: boolean; matchRequest: any }> => {
    try {
      const response = await api.post<{ success: boolean; matchRequest: any }>(`/api/users/${userId}/like`);
      return response.data;
    } catch (error: any) {
      if (error.response?.data?.error) {
        const errorMessage = error.response.data.error;
        const customError = new Error(errorMessage);
        (customError as any).response = error.response;
        throw customError;
      }
      throw error;
    }
  },

  dislike: async (userId: number): Promise<{ success: boolean; matchRequest: any }> => {
    try {
      const response = await api.post<{ success: boolean; matchRequest: any }>(`/api/users/${userId}/dislike`);
      return response.data;
    } catch (error: any) {
      if (error.response?.data?.error) {
        const errorMessage = error.response.data.error;
        const customError = new Error(errorMessage);
        (customError as any).response = error.response;
        throw customError;
      }
      throw error;
    }
  },

  superlike: async (userId: number): Promise<{ success: boolean; matchRequest: any }> => {
    try {
      const response = await api.post<{ success: boolean; matchRequest: any }>(`/api/users/${userId}/superlike`);
      return response.data;
    } catch (error: any) {
      if (error.response?.data?.error) {
        const errorMessage = error.response.data.error;
        const customError = new Error(errorMessage);
        (customError as any).response = error.response;
        throw customError;
      }
      throw error;
    }
  },

  wink: async (userId: number): Promise<{ success: boolean; matchRequest: any }> => {
    const response = await api.post<{ success: boolean; matchRequest: any }>(`/api/users/${userId}/wink`);
    return response.data;
  },

  getMatches: async (): Promise<{ matches: Match[] }> => {
    const response = await api.get<{ matches: Match[] }>('/api/matches');
    return response.data;
  },

  getReceivedLikes: async (): Promise<{ likes: Array<{ id: number; type: string; user: User; createdAt: string }> }> => {
    const response = await api.get<{ likes: Array<{ id: number; type: string; user: User; createdAt: string }> }>('/api/likes/received');
    return response.data;
  },

  getMatch: async (id: number): Promise<Match> => {
    const response = await api.get<Match>(`/api/matches/${id}`);
    return response.data;
  },

  getMessages: async (matchId: number, limit: number = 50, offset: number = 0): Promise<{ messages: any[] }> => {
    const response = await api.get<{ messages: any[] }>(
      `/api/matches/${matchId}/messages?limit=${limit}&offset=${offset}`
    );
    return response.data;
  },

  sendMessage: async (matchId: number, content: string): Promise<any> => {
    const response = await api.post(`/api/matches/${matchId}/message`, { content });
    return response.data;
  },
};

// API Gamification
export const gamificationAPI = {
  getGamification: async (): Promise<GamificationData> => {
    const response = await api.get<GamificationData>('/api/me/gamification');
    return response.data;
  },

  getLeaderboard: async (limit: number = 10): Promise<{ leaderboard: any[] }> => {
    const response = await api.get<{ leaderboard: any[] }>(`/api/leaderboard?limit=${limit}`);
    return response.data;
  },
};

// API Notifications
export const notificationAPI = {
  getNotifications: async (limit: number = 50, offset: number = 0): Promise<{ notifications: Notification[]; unreadCount: number }> => {
    const response = await api.get<{ notifications: Notification[]; unreadCount: number }>(
      `/api/notifications?limit=${limit}&offset=${offset}`
    );
    return response.data;
  },

  markAsRead: async (id: number): Promise<{ success: boolean }> => {
    const response = await api.post<{ success: boolean }>(`/api/notifications/${id}/read`);
    return response.data;
  },

  markAllAsRead: async (): Promise<{ success: boolean }> => {
    const response = await api.post<{ success: boolean }>('/api/notifications/read-all');
    return response.data;
  },
};

// API Reports
export const reportAPI = {
  createReport: async (targetUserId: number, reason: string, details?: string): Promise<{ success: boolean; report: any }> => {
    const response = await api.post<{ success: boolean; report: any }>('/api/reports', {
      targetUserId,
      reason,
      details,
    });
    return response.data;
  },
};

export default api;

