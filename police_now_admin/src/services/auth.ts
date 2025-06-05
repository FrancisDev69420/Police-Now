import api from './api';
import { AxiosError } from 'axios';

interface LoginCredentials {
  email: string;
  password: string;
}

interface AuthResponse {
  token: string;
  user: {
    id: string;
    email: string;
    name: string;
    role: {
      role: string;
    };
  };
}

export const login = async (credentials: LoginCredentials): Promise<AuthResponse> => {
  try {
    console.log('Attempting login with:', { username: credentials.email });
    const response = await api.post<AuthResponse>('/login', {
      username: credentials.email,
      password: credentials.password
    });
    console.log('Login response:', response.data);
      const { token, user } = response.data;
    
    // Check if user is an admin
    if (!user.role || user.role.role !== 'admin') {
      throw new Error('Unauthorized. Admin access required.');
    }
    
    localStorage.setItem('token', token);
    return response.data;
  } catch (error) {
    console.error('Login error:', error);
    if (error instanceof AxiosError) {
      if (error.response) {
        // The request was made and the server responded with a status code
        // that falls out of the range of 2xx
        console.error('Error response:', error.response.data);
        throw new Error(error.response.data.message || 'Login failed');
      } else if (error.request) {
        // The request was made but no response was received
        console.error('No response received:', error.request);
        throw new Error('No response from server. Please check your connection.');
      }
    }
    // Something happened in setting up the request that triggered an Error
    console.error('Error setting up request:', error);
    throw new Error('Failed to send login request');
  }
};

export const logout = () => {
  localStorage.removeItem('token');
};

export const getCurrentUser = async () => {
  try {
    const response = await api.get('/user');
    return response.data;
  } catch (error) {
    console.error('Get current user error:', error);
    throw error;
  }
};

export const isAuthenticated = () => {
  return !!localStorage.getItem('token');
}; 