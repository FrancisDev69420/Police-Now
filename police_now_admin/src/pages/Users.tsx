import React, { useState, useEffect } from 'react';
import { Search, Plus, Users as UsersIcon, Shield, AlertCircle } from 'lucide-react';
import Layout from '../components/common/Layout';
import type { OfficerUser, ResidentUser } from '../types/user';
import { userService } from '../services/users';
import OfficerCard from '../components/users/OfficerCard';
import ResidentCard from '../components/users/ResidentCard';
import CreateOfficerModal from '../components/users/CreateOfficerModal';

type TabType = 'officers' | 'residents';

const Users: React.FC = () => {
  const [activeTab, setActiveTab] = useState<TabType>('officers');
  const [searchTerm, setSearchTerm] = useState('');
  const [officers, setOfficers] = useState<OfficerUser[]>([]);
  const [residents, setResidents] = useState<ResidentUser[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [isCreateModalOpen, setIsCreateModalOpen] = useState(false);

  // Load data on component mount
  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      setLoading(true);
      setError(null);
      
      const [officersData, residentsData] = await Promise.all([
        userService.getOfficers(),
        userService.getResidents()
      ]);
      
      setOfficers(officersData);
      setResidents(residentsData);
    } catch (err) {
      setError('Failed to load users data');
      console.error('Error loading users:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleCreateOfficer = async (officerData: any) => {
    try {
      await userService.createOfficer(officerData);
      await loadData(); // Reload data after creating
      setIsCreateModalOpen(false);
    } catch (err) {
      console.error('Error creating officer:', err);
      throw err; // Let the modal handle the error
    }
  };

  const handleDeleteOfficer = async (officer: OfficerUser) => {
    if (window.confirm('Are you sure you want to delete this officer?')) {
      try {
        await userService.deleteOfficer(officer.id);
        await loadData(); // Reload data after deletion
      } catch (err) {
        console.error('Error deleting officer:', err);
        alert('Failed to delete officer');
      }
    }
  };

  const handleEditOfficer = (officer: OfficerUser) => {
    // TODO: Implement edit functionality
    console.log('Edit officer:', officer);
    alert('Edit functionality coming soon!');
  };

  const handleEditResident = (resident: ResidentUser) => {
    // TODO: Implement edit functionality
    console.log('Edit resident:', resident);
    alert('Edit functionality coming soon!');
  };

  const handleDeleteResident = (resident: ResidentUser) => {
    // TODO: Implement delete functionality
    console.log('Delete resident:', resident);
    alert('Delete functionality coming soon!');
  };
  // Filter data based on search term
  const filteredOfficers = officers.filter(officer =>
    officer.full_name?.toLowerCase().includes(searchTerm.toLowerCase()) ||
    officer.email?.toLowerCase().includes(searchTerm.toLowerCase()) ||
    officer.officer?.badge_number?.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const filteredResidents = residents.filter(resident =>
    resident.full_name?.toLowerCase().includes(searchTerm.toLowerCase()) ||
    resident.email?.toLowerCase().includes(searchTerm.toLowerCase()) ||
    resident.phone_number?.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const activeOfficers = officers.filter(officer => officer.officer?.status === 'active').length;
  const verifiedResidents = residents.filter(resident => resident.is_verified).length;
  if (loading) {
    return (
      <Layout>
        <div className="flex items-center justify-center h-full">
          <div className="text-center">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
            <p className="mt-4 text-gray-600">Loading users...</p>
          </div>
        </div>
      </Layout>
    );
  }

  if (error) {
    return (
      <Layout>
        <div className="flex items-center justify-center h-full">
          <div className="text-center">
            <AlertCircle className="h-12 w-12 text-red-500 mx-auto" />
            <h2 className="mt-4 text-xl font-semibold text-gray-900">Error Loading Users</h2>
            <p className="mt-2 text-gray-600">{error}</p>
            <button
              onClick={loadData}
              className="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            >
              Try Again
            </button>
          </div>
        </div>
      </Layout>
    );
  }
  return (
    <Layout>
      <div className="space-y-6">
        {/* Header */}
        <div>
          <h1 className="text-3xl font-bold text-gray-900">User Management</h1>
          <p className="mt-2 text-gray-600">Manage officers and residents in the system</p>
        </div>

        {/* Statistics Cards */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div className="bg-white rounded-lg shadow p-6">
            <div className="flex items-center">
              <Shield className="h-8 w-8 text-blue-600" />
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600">Total Officers</p>
                <p className="text-2xl font-bold text-gray-900">{officers.length}</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-lg shadow p-6">
            <div className="flex items-center">
              <Shield className="h-8 w-8 text-green-600" />
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600">Active Officers</p>
                <p className="text-2xl font-bold text-gray-900">{activeOfficers}</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-lg shadow p-6">            <div className="flex items-center">
              <UsersIcon className="h-8 w-8 text-purple-600" />
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600">Total Residents</p>
                <p className="text-2xl font-bold text-gray-900">{residents.length}</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-lg shadow p-6">            
            <div className="flex items-center">
              <UsersIcon className="h-8 w-8 text-teal-600" />
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600">Verified Residents</p>
                <p className="text-2xl font-bold text-gray-900">{verifiedResidents}</p>
              </div>
            </div>
          </div>
        </div>        {/* Controls */}
        <div className="bg-white rounded-lg shadow">
          <div className="p-6">
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
              {/* Search */}
              <div className="relative flex-1 max-w-md">
                <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-5 w-5" />
                <input
                  type="text"
                  placeholder="Search users..."
                  className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                />
              </div>

              {/* Create Officer Button */}
              {activeTab === 'officers' && (
                <button
                  onClick={() => setIsCreateModalOpen(true)}
                  className="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                  <Plus className="h-5 w-5 mr-2" />
                  Create Officer
                </button>
              )}
            </div>

            {/* Tabs */}
            <div className="mt-6 border-b border-gray-200">
              <nav className="-mb-px flex space-x-8">
                <button
                  onClick={() => setActiveTab('officers')}
                  className={`py-2 px-1 border-b-2 font-medium text-sm ${
                    activeTab === 'officers'
                      ? 'border-blue-500 text-blue-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  }`}
                >
                  Officers ({officers.length})
                </button>
                <button
                  onClick={() => setActiveTab('residents')}
                  className={`py-2 px-1 border-b-2 font-medium text-sm ${
                    activeTab === 'residents'
                      ? 'border-blue-500 text-blue-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  }`}
                >
                  Residents ({residents.length})
                </button>
              </nav>
            </div>
          </div>
        </div>        {/* Content */}
        <div>
          {activeTab === 'officers' ? (
            <div>
              {filteredOfficers.length === 0 ? (
                <div className="bg-white rounded-lg shadow p-12 text-center">
                  <Shield className="h-12 w-12 text-gray-400 mx-auto" />
                  <h3 className="mt-4 text-lg font-medium text-gray-900">
                    {searchTerm ? 'No officers found' : 'No officers yet'}
                  </h3>
                  <p className="mt-2 text-gray-500">
                    {searchTerm 
                      ? 'Try adjusting your search terms'
                      : 'Get started by creating your first officer'
                    }
                  </p>
                  {!searchTerm && (
                    <button
                      onClick={() => setIsCreateModalOpen(true)}
                      className="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                      Create Officer
                    </button>
                  )}
                </div>
              ) : (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  {filteredOfficers.map((officer) => (
                    <OfficerCard
                      key={officer.id}
                      officer={officer}
                      onEdit={handleEditOfficer}
                      onDelete={handleDeleteOfficer}
                    />
                  ))}
                </div>
              )}
            </div>
          ) : (
            <div>
              {filteredResidents.length === 0 ? (                <div className="bg-white rounded-lg shadow p-12 text-center">
                  <UsersIcon className="h-12 w-12 text-gray-400 mx-auto" />
                  <h3 className="mt-4 text-lg font-medium text-gray-900">
                    {searchTerm ? 'No residents found' : 'No residents yet'}
                  </h3>
                  <p className="mt-2 text-gray-500">
                    {searchTerm 
                      ? 'Try adjusting your search terms'
                      : 'Residents will appear here once they register through the mobile app'
                    }
                  </p>
                </div>
              ) : (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  {filteredResidents.map((resident) => (
                    <ResidentCard 
                      key={resident.id} 
                      resident={resident} 
                      onEdit={handleEditResident}
                      onDelete={handleDeleteResident}
                    />
                  ))}
                </div>
              )}
            </div>
          )}
        </div>
      </div>

      {/* Create Officer Modal */}
      <CreateOfficerModal
        isOpen={isCreateModalOpen}
        onClose={() => setIsCreateModalOpen(false)}
        onSubmit={handleCreateOfficer}
      />
    </Layout>
  );
};

export default Users;
