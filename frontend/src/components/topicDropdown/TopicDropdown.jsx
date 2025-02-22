import { RiArrowDropDownLine } from "react-icons/ri";
import React from 'react';

const categoryOptions = [
    { value: '1', label: 'Work' },
    { value: '2', label: 'Education' },
    { value: '3', label: 'Family' },
    { value: '4', label: 'Health' },
];

const TopicDropdown = ({ onTopicSelect }) => {
    const handleSelect = (event) => {
        const selectedId = event.target.value ? parseInt(event.target.value) : null;
        onTopicSelect(selectedId);
    };

    return (
        <div className="relative w-full sm:w-auto">
            <select
                name="category"
                className="
                    outline-none bg-white border-2 border-transparent rounded-lg px-1 py-1 text-gray-700
                    hover:border-purple-500 focus:border-purple-500 
                    cursor-pointer transition-all duration-200 shadow-sm hover:shadow-lg
                "
                onChange={handleSelect}
                required // Ensures selection
                defaultValue="" // Default value is empty to trigger validation
            >
                <option value="" disabled>Select a Topic</option> {/* Placeholder */}
                {categoryOptions.map((option) => (
                    <option key={option.value} value={option.value}>
                        {option.label}
                    </option>
                ))}
            </select>
        </div>
    );
};

export default TopicDropdown;
