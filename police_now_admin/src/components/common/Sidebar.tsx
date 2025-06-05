import React from 'react';
import { Link } from 'react-router-dom';

const Sidebar: React.FC = () => {
  return (
    <div className="bg-gray-800 text-white w-64 min-h-screen">
      <div className="p-4">
        <nav className="mt-5">
          <Link
            to="/dashboard"
            className="block px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-md"
          >
            Dashboard
          </Link>
          <Link
            to="/users"
            className="block px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-md"
          >
            Users
          </Link>
        </nav>
      </div>
    </div>
  );
};

export default Sidebar; 