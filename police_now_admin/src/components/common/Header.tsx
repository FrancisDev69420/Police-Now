import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../../hooks/useAuth';


const Header: React.FC = () => {
  // Header component with logout confirmation modal
  const navigate = useNavigate();

  // Using custom hook for authentication
  const { logout } = useAuth();

  // State to manage logout confirmation modal visibility and animation
  const [showConfirm, setShowConfirm] = useState(false);

  // State to manage animation for the confirmation modal
  const [isAnimating, setIsAnimating] = useState(false);

  // Effect to trigger animation when the confirmation modal is shown
  useEffect(() => {
    if (showConfirm) {
      setIsAnimating(true);
    }
  }, [showConfirm]);

  // Handler for logout button click, which shows the confirmation modal
  const handleLogoutClick = () => {
    setShowConfirm(true);
  };

  // Handlers for confirmation modal actions, confirming or canceling the logout action
  const handleConfirmLogout = async () => {
    setShowConfirm(false);
    setIsAnimating(false);
    try {
      await logout();
      navigate('/login');
    } catch (error) {
      console.error('Logout error:', error);
      navigate('/login');
    }
  };

  // Handler for canceling the logout action, hiding the confirmation modal
  const handleCancelLogout = () => {
    setShowConfirm(false);
    setIsAnimating(false);
  };

  return (
    <>
      <header className="bg-white shadow">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between h-16">
            <div className="flex">
              <div className="flex-shrink-0 flex items-center">
                <h1 className="text-xl font-bold">Police Now Admin</h1>
              </div>
            </div>
            <div className="flex items-center">
              <button
                onClick={handleLogoutClick}
                className="ml-4 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md transition-colors"
              >
                Logout
              </button>
            </div>
          </div>
        </div>
      </header>      
      
      {/* Confirmation Modal */}
      {showConfirm && (
        <div 
          className={`fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-all duration-300 ease-in-out ${
            isAnimating ? 'opacity-100' : 'opacity-0'
          }`}
        >
          <div 
            className={`bg-white p-6 rounded-lg shadow-xl max-w-sm w-full mx-4 transform transition-all duration-200 ease-out ${
              isAnimating ? 'scale-100 opacity-100' : 'scale-95 opacity-0'
            }`}
          >
            <h3 className="text-lg font-medium text-gray-900 mb-4">
              Confirm Logout
            </h3>
            <p className="text-sm text-gray-500 mb-6">
              Are you sure you want to log out of your admin session?
            </p>
            <div className="flex gap-3 justify-end">
              <button
                onClick={handleCancelLogout}
                className="px-4 py-2 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors"
              >
                Cancel
              </button>
              <button
                onClick={handleConfirmLogout}
                className="px-4 py-2 text-sm text-white bg-red-600 hover:bg-red-700 rounded-md transition-colors"
              >
                Logout
              </button>
            </div>
          </div>
        </div>
      )}
    </>
  );
};

export default Header;