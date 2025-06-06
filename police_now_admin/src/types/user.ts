// Types for User Management
export interface UserRole {
  id: number;
  role: 'admin' | 'officer' | 'resident';
  permissions: string;
  description: string;
}

export interface BaseUser {
  id: number;
  username: string;
  email: string;
  full_name: string;
  phone_number?: string;
  address?: string;
  role_id: number;
  registration_date: string;
  is_verified: boolean;
  verification_status: string;
  profile_image_url?: string;
  role: UserRole;
}

export interface OfficerProfile {
  id: number;
  user_id: number;
  badge_number: string;
  rank: string;
  department: string;
  status: string;
  service_start_date: string;
  shift_start?: string;
  shift_end?: string;
  specialization?: string;
  on_duty: boolean;
}

export interface ResidentProfile {
  id: number;
  user_id: number;
  emergency_contact_name?: string;
  emergency_contact_number?: string;
  medical_info?: string;
  residential_address?: string;
  city?: string;
  province?: string;
  postal_code?: string;
}

export interface OfficerUser extends BaseUser {
  officer: OfficerProfile;
}

export interface ResidentUser extends BaseUser {
  resident: ResidentProfile;
}

export type User = OfficerUser | ResidentUser;

export interface UsersResponse {
  officers: OfficerUser[];
  residents: ResidentUser[];
}
