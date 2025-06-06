import React from 'react';
import type { ResidentUser } from '../../types/user';

interface ResidentCardProps {
  resident: ResidentUser;
  onEdit: (resident: ResidentUser) => void;
  onDelete: (resident: ResidentUser) => void;
}

const ResidentCard: React.FC<ResidentCardProps> = ({ resident, onEdit, onDelete }) => {
  return (
    <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
      <div className="flex items-start justify-between">
        <div className="flex-1">
          <div className="flex items-center gap-3 mb-2">
            <div className="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
              <span className="text-green-600 font-semibold text-lg">
                {resident.full_name.split(' ').map(n => n[0]).join('').slice(0, 2)}
              </span>
            </div>
            <div>
              <h3 className="text-lg font-semibold text-gray-900">{resident.full_name}</h3>
              <p className="text-sm text-gray-500">@{resident.username}</p>
            </div>
          </div>
          
          <div className="space-y-2 mb-4">
            <div className="flex items-center gap-4">
              <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                resident.is_verified 
                  ? 'bg-green-100 text-green-800' 
                  : 'bg-yellow-100 text-yellow-800'
              }`}>
                {resident.verification_status}
              </span>
            </div>
            
            <div className="text-sm">
              <span className="text-gray-500">Email:</span>
              <span className="ml-2">{resident.email}</span>
            </div>
            
            {resident.phone_number && (
              <div className="text-sm">
                <span className="text-gray-500">Phone:</span>
                <span className="ml-2">{resident.phone_number}</span>
              </div>
            )}
            
            {resident.resident.residential_address && (
              <div className="text-sm">
                <span className="text-gray-500">Address:</span>
                <span className="ml-2">{resident.resident.residential_address}</span>
                {resident.resident.city && resident.resident.province && (
                  <span className="block ml-2 text-gray-400">
                    {resident.resident.city}, {resident.resident.province}
                    {resident.resident.postal_code && ` ${resident.resident.postal_code}`}
                  </span>
                )}
              </div>
            )}
            
            {resident.resident.emergency_contact_name && (
              <div className="text-sm">
                <span className="text-gray-500">Emergency Contact:</span>
                <span className="ml-2">{resident.resident.emergency_contact_name}</span>
                {resident.resident.emergency_contact_number && (
                  <span className="block ml-2 text-gray-400">
                    {resident.resident.emergency_contact_number}
                  </span>
                )}
              </div>
            )}
            
            <div className="text-sm">
              <span className="text-gray-500">Registered:</span>
              <span className="ml-2">{new Date(resident.registration_date).toLocaleDateString()}</span>
            </div>
            
            <div className="text-sm">
              <span className="text-gray-500">Verified:</span>
              <span className={`ml-2 ${resident.is_verified ? 'text-green-600' : 'text-red-600'}`}>
                {resident.is_verified ? 'Yes' : 'No'}
              </span>
            </div>
          </div>
        </div>
      </div>
      
      <div className="flex gap-2 pt-4 border-t border-gray-200">
        <button
          onClick={() => onEdit(resident)}
          className="flex-1 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors text-sm font-medium"
        >
          Edit
        </button>
        <button
          onClick={() => onDelete(resident)}
          className="flex-1 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors text-sm font-medium"
        >
          Delete
        </button>
      </div>
    </div>
  );
};

export default ResidentCard;
