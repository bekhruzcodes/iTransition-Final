import { Star } from 'lucide-react'
import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'

const mockPopularTemplates = [
    { id: 1, title: 'Customer Satisfaction', uses: 15420, author: 'John Doe' },
    { id: 2, title: 'Job Application', uses: 12350, author: 'Jane Smith' },
    { id: 3, title: 'Event Registration', uses: 10200, author: 'Michael Lee' },
    { id: 4, title: 'Product Feedback', uses: 9870, author: 'Sarah Williams' },
    { id: 5, title: 'Contact Form', uses: 8940, author: 'David Brown' }
];

const mockTags = [
    { name: 'feedback', count: 45 },
    { name: 'survey', count: 38 },
    { name: 'registration', count: 32 },
    { name: 'customer', count: 28 },
    { name: 'events', count: 25 },
    { name: 'hr', count: 20 },
    { name: 'education', count: 18 }
];



const Popular = () => {
    const [popularTemplates, setPopularTemplates] = useState([]);
    const [tags, setTags] = useState([]);

    useEffect(() => {
        setPopularTemplates(mockPopularTemplates);
        setTags(mockTags);
    }, []);




  return (
    <div className="mb-12">
                    <h2 className="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <Star className="w-6 h-6 mr-2 text-purple-600" />
                        Most Popular Templates
                    </h2>
                    <div className="bg-white rounded-lg shadow-sm overflow-hidden">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Template
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Author
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Uses
                                </th>
                            </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                            {popularTemplates.map((template) => (
                                <tr key={template.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <Link to={`/templates/${template.id}`} className="text-purple-600 hover:text-purple-700">
                                            {template.title}
                                        </Link>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-gray-500">
                                        {template.author}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-gray-500">
                                        {template.uses.toLocaleString()}
                                    </td>
                                </tr>
                            ))}
                            </tbody>
                        </table>
                    </div>
                </div>
  )
}

export default Popular