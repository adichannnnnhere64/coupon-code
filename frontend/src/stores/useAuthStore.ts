// src/stores/useAuthStore.ts
import axios from 'axios';
import { create } from 'zustand';

const api = axios.create({
  baseURL: 'http://localhost:8000/api', // ✅ API-only base URL
});

interface AuthState {
  user: any | null;
  token: string | null;
  isAuthenticated: boolean;
  login: (email: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
  checkAuth: () => Promise<void>;
}

export const useAuthStore = create<AuthState>((set, get) => ({
  user: null,
  token: localStorage.getItem('auth_token'),
  isAuthenticated: false,

  login: async (email: string, password: string) => {
    try {
      const response = await api.post('/login', { email, password });
      const { user, token } = response.data;

      // ✅ Store token
      localStorage.setItem('auth_token', token);

      // ✅ Set Authorization header for all future requests
      api.defaults.headers.common['Authorization'] = `Bearer ${token}`;

      set({
        user,
        token,
        isAuthenticated: true
      });
    } catch (error: any) {
      if (error.response?.status === 422) {
        const message = error.response.data.message || 'Invalid credentials';
        throw new Error(message);
      }
      throw new Error('Login failed. Please try again.');
    }
  },

  logout: async () => {
    try {
      await api.post('/logout');
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      // ✅ Clean up
      localStorage.removeItem('auth_token');
      delete api.defaults.headers.common['Authorization'];

      set({
        user: null,
        token: null,
        isAuthenticated: false
      });
    }
  },

  checkAuth: async () => {
    const token = localStorage.getItem('auth_token');

    if (!token) {
      set({ isAuthenticated: false, user: null });
      return;
    }

    try {
      // ✅ Set token for this request
      api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
      const response = await api.get('/me');

      set({
        user: response.data,
        isAuthenticated: true
      });
    } catch (error) {
      // ✅ Token invalid/expired
      localStorage.removeItem('auth_token');
      delete api.defaults.headers.common['Authorization'];
      set({ user: null, token: null, isAuthenticated: false });
    }
  },
}));

// ✅ Initialize on app start
useAuthStore.getState().checkAuth();
