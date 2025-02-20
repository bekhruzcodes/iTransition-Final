import { Layout } from 'lucide-react'
import React, { useEffect, useState } from 'react'
import Card from '../card/Card';

// const mockLatestTemplates = [
//     {
//         id: 1,
//         title: 'Customer Satisfaction',
//         description: 'A survey for customer satisfaction.',
//         imageUrl: 'https://via.placeholder.com/150',
//         user: {
//             name: 'John Doe'
//         }
//     },
// ]


const Recent = () => {
    const [latestTemplates, setLatestTemplates] = useState([]);

    // useEffect(() => {
    //     setLatestTemplates(mockLatestTemplates);
    // }, []);



    useEffect(() => {
        const fetchLatestTemplates = async () => {
            try {
                const response = await fetch('http://127.0.0.1:8000/api/templates/list');
                const data = await response.json();

                console.log("Full API Response:", data);

                if (Array.isArray(data?.templates)) {
                    setLatestTemplates(data.templates);
                } else {
                    console.error("Error: data.templates is not an array", data.templates);
                    setLatestTemplates([]);
                }
            } catch (error) {
                console.error('Error fetching latest templates:', error);
                setLatestTemplates([]);
            }
        };

        fetchLatestTemplates();
    }, []);





    return (
        <div className="mb-12">
            <h2 className="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <Layout className="w-6 h-6 mr-2 text-purple-600" />
                Latest Templates
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {latestTemplates.map((template) => (
                    <Card template={template} key={template.id}/>
                ))}
            </div>
        </div>
    )
}

export default Recent