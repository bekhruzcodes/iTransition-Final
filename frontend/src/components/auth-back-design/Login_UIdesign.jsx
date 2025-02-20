import React from 'react'
import { Circle, CheckCircle } from 'lucide-react'
import login_img from "../../images/login.JPG"

const Login_UIdesign = () => {
  return (
    <div className="hidden md:flex md:w-1/2 xl:w-1/2 min-h-screen bg-purple-600 relative overflow-hidden">
                <div className="absolute top-10 left-10 w-32 h-32 bg-purple-500 rounded-full opacity-30" />
                <div className="absolute bottom-10 right-10 w-40 h-40 bg-purple-500 rounded-full opacity-30" />

                <div className="relative flex flex-col items-center justify-center w-full p-8">
                    <div className="absolute top-20 left-20 flex items-center space-x-2">
                        <Circle className="w-4 h-4 text-purple-300" />
                        <div className="w-32 h-2 bg-purple-500 rounded-full" />
                    </div>
                    <div className="absolute top-32 left-28 flex items-center space-x-2">
                        <CheckCircle className="w-4 h-4 text-purple-300" />
                        <div className="w-24 h-2 bg-purple-500 rounded-full" />
                    </div>

                    <div className="relative bg-white rounded-2xl p-6 shadow-xl">
                        <div className="absolute top-4 left-4 right-4 flex items-center space-x-2">
                            <div className="flex space-x-1">
                                <div className="w-3 h-3 rounded-full bg-red-400" />
                                <div className="w-3 h-3 rounded-full bg-yellow-400" />
                                <div className="w-3 h-3 rounded-full bg-green-400" />
                            </div>
                            <div className="w-32 h-2 bg-gray-200 rounded-full" />
                        </div>

                        <img
                            src={login_img}
                            alt="Forms illustration"
                            className="max-w-md w-full object-cover mt-8"
                        />
                    </div>

                    <div className="mt-8 text-center text-white">
                        <h2 className="text-3xl font-bold">Welcome to Forms</h2>
                        <p className="mt-2 text-purple-200">Create, manage, and analyze your forms with ease</p>
                    </div>
                </div>
            </div>
  )
}

export default Login_UIdesign