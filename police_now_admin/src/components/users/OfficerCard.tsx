import React from 'react';
import type { OfficerUser } from '../../types/user';

interface OfficerCardProps {
  officer: OfficerUser;
  onEdit: (officer: OfficerUser) => void;
  onDelete: (officer: OfficerUser) => void;
}

const OfficerCard: React.FC<OfficerCardProps> = ({ officer, onEdit, onDelete }) => {
  const getStatusColor = (status: string) => {
    switch (status.toLowerCase()) {
      case 'active':
        return 'bg-green-100 text-green-800';
      case 'inactive':
        return 'bg-red-100 text-red-800';
      case 'suspended':
        return 'bg-yellow-100 text-yellow-800';
      default:
        return 'bg-gray-100 text-gray-800';
    }
  };

  const getDutyStatusColor = (onDuty: boolean) => {
    return onDuty 
      ? 'bg-blue-100 text-blue-800' 
      : 'bg-gray-100 text-gray-800';
  };

  return (
    <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
      <div className="flex items-start justify-between">
        <div className="flex-1">
          <div className="flex items-center gap-3 mb-2">
            <div className="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
              <span className="text-blue-600 font-semibold text-lg">
                {officer.full_name.split(' ').map(n => n[0]).join('').slice(0, 2)}
              </span>
            </div>
            <div>
              <h3 className="text-lg font-semibold text-gray-900">{officer.full_name}</h3>
              <p className="text-sm text-gray-500">Badge #{officer.officer.badge_number}</p>
            </div>
          </div>
          
          <div className="space-y-2 mb-4">
            <div className="flex items-center gap-4">
              <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(officer.officer.status)}`}>
                {officer.officer.status}
              </span>
              <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getDutyStatusColor(officer.officer.on_duty)}`}>
                {officer.officer.on_duty ? 'On Duty' : 'Off Duty'}
              </span>
            </div>
            
            <div className="grid grid-cols-2 gap-4 text-sm">
              <div>
                <span className="text-gray-500">Rank:</span>
                <span className="ml-2 font-medium">{officer.officer.rank}</span>
              </div>
              <div>
                <span className="text-gray-500">Department:</span>
                <span className="ml-2 font-medium">{officer.officer.department}</span>
              </div>
            </div>
            
            {officer.officer.specialization && (
              <div className="text-sm">
                <span className="text-gray-500">Specialization:</span>
                <span className="ml-2 font-medium">{officer.officer.specialization}</span>
              </div>
            )}
            
            <div className="text-sm">
              <span className="text-gray-500">Email:</span>
              <span className="ml-2">{officer.email}</span>
            </div>
            
            {officer.phone_number && (
              <div className="text-sm">
                <span className="text-gray-500">Phone:</span>
                <span className="ml-2">{officer.phone_number}</span>
              </div>
            )}
            
            <div className="text-sm">
              <span className="text-gray-500">Service Start:</span>
              <span className="ml-2">{new Date(officer.officer.service_start_date).toLocaleDateString()}</span>
            </div>
            
            <div className="text-sm">
              <span className="text-gray-500">Verified:</span>
              <span className={`ml-2 ${officer.is_verified ? 'text-green-600' : 'text-red-600'}`}>
                {officer.is_verified ? 'Yes' : 'No'}
              </span>
            </div>
          </div>
        </div>
      </div>
      
      <div className="flex gap-2 pt-4 border-t border-gray-200">
        <button
          onClick={() => onEdit(officer)}
          className="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm font-medium"
        >
          Edit
        </button>
        <button
          onClick={() => onDelete(officer)}
          className="flex-1 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors text-sm font-medium"
        >
          Delete
        </button>
      </div>
    </div>
  );
};

export default OfficerCard;
