import { Layout } from 'lucide-react'
import React, { useEffect, useState } from 'react'
import Card from '../card/Card';
import img1 from '../../images/login.JPG'
import img2 from '../../images/register.jpg'
import img3 from '../../images/general.jpeg'

const mockLatestTemplates = [
    {
        id: 1,
        title: 'Customer Satisfaction',
        description: 'A survey for customer satisfaction.',
        imageUrl: img1,
        user: {
            name: 'John Doe'
        },
        tags: [
            { "id": 2, "name": "work" },
            { "id": 3, "name": "study" }
        ],

    },
    {
        id: 2,
        title: 'Job Application',
        description: 'A form for job applications.',
        imageUrl: img2,
        user: {
            name: 'Jane Smith'
        },
        tags: [
            { "id": 2, "name": "work" },
            { "id": 3, "name": "study" }
        ],
    },
    {
        id: 3,
        title: 'Event Registration',
        description: 'A form for event registrations.',
        imageUrl: img3,
        user: {
            name: 'Michael Lee'
        },
        tags: [
            { "id": 2, "name": "work" },
            { "id": 3, "name": "study" }
        ],
    },
    {
        id: 4,
        title: 'Product Feedback',
        description: 'A form for product feedback.',
        imageUrl: 'https://via.placeholder.com/150',
        user: {
            name: 'Sarah Williams'
        },
        tags: [
            { "id": 2, "name": "work" },
            { "id": 3, "name": "study" }
        ],
    },
    {
        id: 5,
        title: 'Contact Form',
        description: 'A form for contact information.',
        imageUrl: 'https://via.placeholder.com/150',
        user: {
            name: 'David Brown'
        },
        tags: [
            { "id": 2, "name": "work" },
            { "id": 3, "name": "study" }
        ],
    }
]


const Recent = () => {
    const [latestTemplates, setLatestTemplates] = useState([]);
    const [showAll, setShowAll] = useState(false);

    useEffect(() => {
        setLatestTemplates(mockLatestTemplates);
    }, []);



    // useEffect(() => {
    //     const fetchLatestTemplates = async () => {
    //         try {
    //             const response = await fetch('http://127.0.0.1:8000/api/templates/list');
    //             const data = await response.json();

    //             console.log("Full API Response:", data);

    //             if (Array.isArray(data?.templates)) {
    //                 setLatestTemplates(data.templates);
    //             } else {
    //                 console.error("Error: data.templates is not an array", data.templates);
    //                 setLatestTemplates([]);
    //             }
    //         } catch (error) {
    //             console.error('Error fetching latest templates:', error);
    //             setLatestTemplates([]);
    //         }
    //     };

    //     fetchLatestTemplates();
    // }, []);





    return (
        <div className="mb-12">
            <h2 className="text-2xl font-bold text-gray-900 mb-6 flex justify-between">
                <span className="flex items-center">
                    <Layout className="w-6 h-6 mr-2 text-purple-600" />
                    Latest Templates
                </span>
                <button onClick={()=> setShowAll(!showAll)} className='text-sm text-purple-600 ml-2'>
                    {showAll ? 'Show Less' : 'Show All'}
                </button>
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {(showAll ? latestTemplates : latestTemplates?.slice(0, 3)).map((template) => (
                    <Card template={template} key={template.id} />
                ))}
            </div>
        </div>
    )
}

export default Recent