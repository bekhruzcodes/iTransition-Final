import { AiOutlinePlus } from "react-icons/ai"; 
import { Search } from 'lucide-react'
import React, { useState } from 'react'
import { Link } from 'react-router-dom'

const Nav = () => {

    const [searchQuery, setSearchQuery] = useState('');



  return (
    <nav className="bg-white shadow-sm">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between items-center h-16">
                        <div className="flex-1 flex items-center justify-between">
                            <Link to="/" className="text-xl font-bold text-purple-600">
                                Forms
                            </Link>
                            <div className="flex-1 max-w-lg px-4">
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <Search className="h-5 w-5 text-gray-400" />
                                    </div>
                                    <input
                                        type="text"
                                        className="block w-full pl-10 pr-3 py-2 bg-gray-100 border-transparent rounded-md focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                                        placeholder="Search templates..."
                                        value={searchQuery}
                                        onChange={(e) => setSearchQuery(e.target.value)}
                                    />
                                </div>
                            </div>
                            <div className="flex items-center space-x-4">
                                <div className="relative group sm:hidden">
                                    <Link
                                        to="/create-template"
                                        className="bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors duration-200 flex items-center justify-center 
                                                px-2 py-1 sm:px-4 sm:py-2"
                                    >
                                        <span className="block text-3xl"><AiOutlinePlus /></span> 
                                    </Link>

                                    <span className="absolute top-full mt-[5px] text-center left-1/2 -translate-x-1/2 hidden group-hover:block bg-gray-800 text-white text-xs rounded-md px-2 py-1">
                                        Create Template
                                    </span>
                                    </div>
                                    <Link
                                    to="/create-template"
                                    className="hidden sm:flex bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors duration-200 px-4 py-2"
                                    >
                                    Create Template
                                    </Link>
                                
                                <Link
                                    to="/login"
                                    className="text-purple-600 hover:text-purple-700"
                                >
                                    Login
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
  )
}

export default Nav