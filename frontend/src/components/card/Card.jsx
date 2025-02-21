import React from 'react'
import { Link } from 'react-router-dom'
import imgPlaceholder from '../../images/formPlaceholder.png'

const Card = ({ template }) => {
    return (
        <Link
            key={template.id}
            to={`/templates/${template.id}`}
            className="bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200"
        >
            
            <div className="p-6">
                <img
                    src={template.imageUrl ? template.imageUrl : imgPlaceholder}
                    className="w-full h-48 object-cover rounded-md mb-4"
                />
                
                <h3 className="text-lg font-semibold text-gray-900 mb-2">
                    {template.title}
                </h3>
                <p className="text-sm text-gray-600 mb-4">
                    {template.description}
                </p>
                <div className="flex items-center justify-between">
                    <span className="text-sm text-gray-500">
                        By {template?.user.name}
                    </span>
                    <div className="flex gap-2">
                        {template.tags.slice(0, 3).map((tag) => (
                            <span key={tag.id} className="text-xs px-2 py-1 bg-purple-100 text-purple-600 rounded-full">
                                {tag.name}
                            </span>
                        ))}
                    </div>
                </div>
            </div>
        </Link>
    )
}

export default Card