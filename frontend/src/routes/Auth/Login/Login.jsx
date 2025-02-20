import { useState } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';
import { Lock, Mail } from 'lucide-react';
import Login_UIdesign from '../../../components/auth-back-design/Login_UIdesign';

const Login = () => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const navigate = useNavigate();

    const handleLogin = async (e) => {
        e.preventDefault();
        try {
            const response = await axios.post('http://127.0.0.1:8000/api/login', {
                email,
                password,
            });
            localStorage.setItem('token', response.data.token);
            navigate('/dashboard');
        } catch (error) {
            console.error('Login Error:', error.response);
            if (error.response) {
                setError(error.response.data.message || 'Login failed');
            } else if (error.request) {
                setError('No response from server');
            } else {
                setError(error.message);
            }
        }
    };

    return (
        <div className="min-h-screen flex w-full">
            <Login_UIdesign/>
            <div className="flex-1 flex items-center justify-center p-8">
                <div className="max-w-md w-full">
                    <div className="flex flex-col items-center mb-8">
                        <div className="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center">
                            <Lock className="w-10 h-10 text-purple-600" />
                        </div>

                        <h2 className="mt-6 text-2xl font-bold text-gray-900">
                            Welcome back!
                        </h2>
                    </div>

                    {error && (
                        <div className="mb-6 bg-red-50 border border-red-200 text-red-600 p-3 rounded-md text-sm">
                            {error}
                        </div>
                    )}

                    <form className="space-y-4">
                        <div className="relative">
                            <div className="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <Mail className="h-5 w-5 text-gray-400" />
                            </div>
                            <input
                                type="email"
                                required
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                placeholder="r@gmail.com"
                                className="w-full pl-10 pr-3 py-3 bg-gray-100 border-transparent rounded-md focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                            />
                        </div>

                        <div className="relative">
                            <div className="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <Lock className="h-5 w-5 text-gray-400" />
                            </div>
                            <input
                                type="password"
                                required
                                value={password}
                                onChange={(e) => setPassword(e.target.value)}
                                placeholder="••••••"
                                className="w-full pl-10 pr-3 py-3 bg-gray-100 border-transparent rounded-md focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                            />
                        </div>

                        <button
                            type="submit"
                            onClick={handleLogin}
                            className="w-full py-3 px-4 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200"
                        >
                            Sign in
                        </button>
                    </form>

                    <div className="mt-8 text-center">
                        <p className="text-gray-500">New here?</p>
                        <a
                            href="/register"
                            className="mt-1 text-purple-600 hover:text-purple-700 font-medium inline-flex items-center"
                        >
                            Create an account →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Login;