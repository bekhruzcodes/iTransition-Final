import { TagIcon } from 'lucide-react'
import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'

const mockTags = [
    { name: 'feedback', count: 45 },
    { name: 'survey', count: 38 },
    { name: 'registration', count: 32 },
    { name: 'customer', count: 28 },
    { name: 'events', count: 25 },
    { name: 'hr', count: 20 },
    { name: 'education', count: 18 }
];

const TagCloud = () => {
    const [tags, setTags] = useState([]);

    useEffect(() => {
        setTags(mockTags);
    }, []);



    return (
        <div>
            <h2 className="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <TagIcon className="w-6 h-6 mr-2 text-purple-600" />
                Popular Tags
            </h2>
            <div className="bg-white rounded-lg shadow-sm p-6">
                <div className="flex flex-wrap gap-3">
                    {tags.map((tag) => (
                        <Link
                            key={tag.name}
                            to={`/search?tag=${tag.name}`}
                            className="px-4 py-2 bg-purple-50 text-purple-600 rounded-full hover:bg-purple-100 transition-colors duration-200 text-sm flex items-center"
                        >
                            {tag.name}
                            <span className="ml-2 text-xs bg-purple-200 px-2 py-1 rounded-full">
                                {tag.count}
                            </span>
                        </Link>
                    ))}
                </div>
            </div>
        </div>
    )
}

export default TagCloud