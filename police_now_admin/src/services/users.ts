import api from './api';
import type { OfficerUser, ResidentUser } from '../types/user';

export const userService = {
  // Get all officers
  async getOfficers(): Promise<OfficerUser[]> {
    try {
      const response = await api.get('/admin/officers');
      return response.data;
    } catch (error) {
      console.error('Error fetching officers:', error);
      throw error;
    }
  },

  // Get all residents  
  async getResidents(): Promise<ResidentUser[]> {
    try {
      const response = await api.get('/admin/residents');
      return response.data.data;
    } catch (error) {
      console.error('Error fetching residents:', error);
      throw error;
    }
  },

  // Create a new officer
  async createOfficer(officerData: {
    username: string;
    email: string;
    full_name: string;
    phone_number?: string;
    badge_number: string;
    rank: string;
    department: string;
    specialization?: string;
    service_start_date: string;
  }): Promise<{ user: OfficerUser; officer: any; temporary_password: string }> {
    try {
      const response = await api.post('/admin/officers', officerData);
      return response.data;
    } catch (error) {
      console.error('Error creating officer:', error);
      throw error;
    }
  },

  // Update officer
  async updateOfficer(id: number, updateData: Partial<{
    badge_number: string;
    rank: string;
    department: string;
    status: string;
    specialization: string;
    full_name: string;
    phone_number: string;
    address: string;
  }>): Promise<{ message: string; officer: OfficerUser }> {
    try {
      const response = await api.put(`/admin/officers/${id}`, updateData);
      return response.data;
    } catch (error) {
      console.error('Error updating officer:', error);
      throw error;
    }
  },

  // Delete officer
  async deleteOfficer(id: number): Promise<{ message: string }> {
    try {
      const response = await api.delete(`/admin/officers/${id}`);
      return response.data;
    } catch (error) {
      console.error('Error deleting officer:', error);
      throw error;
    }
  },

  // Update resident
  async updateResident(id: number, updateData: Partial<{
    emergency_contact_name: string;
    emergency_contact_number: string;
    medical_info: string;
    residential_address: string;
    city: string;
    province: string;
    postal_code: string;
  }>): Promise<ResidentUser> {
    try {
      const response = await api.put(`/admin/residents/${id}`, updateData);
      return response.data;
    } catch (error) {
      console.error('Error updating resident:', error);
      throw error;
    }
  },

  // Delete resident
  async deleteResident(id: number): Promise<{ message: string }> {
    try {
      const response = await api.delete(`/admin/residents/${id}`);
      return response.data;
    } catch (error) {
      console.error('Error deleting resident:', error);
      throw error;
    }
  }
};
