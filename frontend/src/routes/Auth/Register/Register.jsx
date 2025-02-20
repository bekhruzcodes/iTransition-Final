import {useState} from 'react';
import axios from 'axios';
import {useNavigate} from 'react-router-dom';
import {Lock, Mail, User} from 'lucide-react';
import Auth_design from '../../../components/auth-back-design/Auth_design';


const Register = () => {
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [confirmPassword, setConfirmPassword] = useState('');
    const [error, setError] = useState('');
    const navigate = useNavigate();

    const handleRegister = async (e) => {
        e.preventDefault();
        if (password !== confirmPassword) {
            setError('Passwords do not match');
            return;
        }

        try {
            await axios.post('http://127.0.0.1:8000/api/register', {
                name,
                email,
                password,
            });
            navigate('/login');
        } catch (error) {
            if (error.response && error.response.data) {
                setError(error.response.data.message);
            } else {
                setError('Registration failed');
            }
        }
    };

    return (
        <div className="min-h-screen flex w-[100%]">
            <Auth_design/>
            <div className="flex-1 flex items-center justify-center p-8">
                <div className="max-w-md w-full">
                    <div className="flex flex-col items-center mb-8">
                        <div className="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center">
                            <User className="w-10 h-10 text-purple-600"/>
                        </div>
                        <h2 className="mt-6 text-2xl font-bold text-gray-900">
                            Create account
                        </h2>
                    </div>
                    {error && (

                        <div className="mb-6 bg-red-50 border border-red-200 text-red-600 p-3 rounded-md text-sm">
                            {error}
                        </div>

                    )}
                    <form onSubmit={handleRegister} className="space-y-4">
                        <div className="relative">
                            <div className="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <User className="h-5 w-5 text-gray-400"/>
                            </div>
                            <input
                                type="text"
                                min="2"
                                required
                                value={name}
                                onChange={(e) => setName(e.target.value)}
                                placeholder="Full name"
                                className="w-full pl-10 pr-3 py-3 bg-gray-100 border-transparent rounded-md focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                            />
                        </div>
                        <div className="relative">
                            <div className="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <Mail className="h-5 w-5 text-gray-400"/>
                            </div>
                            <input
                                type="email"
                                required
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                placeholder="Email address"
                                className="w-full pl-10 pr-3 py-3 bg-gray-100 border-transparent rounded-md focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                            />
                        </div>
                        <div className="relative">
                            <div className="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <Lock className="h-5 w-5 text-gray-400"/>
                            </div>
                            <input
                                type="password"
                                required
                                min="6"
                                value={password}
                                onChange={(e) => setPassword(e.target.value)}
                                placeholder="Password"
                                className="w-full pl-10 pr-3 py-3 bg-gray-100 border-transparent rounded-md focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                            />
                        </div>
                        <div className="relative">
                            <div className="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <Lock className="h-5 w-5 text-gray-400"/>
                            </div>
                            <input
                                type="password"
                                required
                                value={confirmPassword}
                                onChange={(e) => setConfirmPassword(e.target.value)}
                                placeholder="Confirm password"
                                className="w-full pl-10 pr-3 py-3 bg-gray-100 border-transparent rounded-md focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                            />
                        </div>
                        <button
                            type="submit"
                            className="w-full py-3 px-4 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200"
                        >
                            Create account
                        </button>
                    </form>
                    <div className="mt-8 text-center">
                        <p className="text-gray-500">Already have an account?</p>
                        <a
                            href="/login"
                            className="mt-1 text-purple-600 hover:text-purple-700 font-medium inline-flex items-center"
                        >
                            Sign in →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Register;