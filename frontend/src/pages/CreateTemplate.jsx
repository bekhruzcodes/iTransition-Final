import React, {useState, useEffect} from 'react';
import {Plus, Trash2, GripVertical, Type, Radio, CheckSquare, List, X} from 'lucide-react';
import TopicDropdown from '../components/topicDropdown/TopicDropdown';

const FormBuilder = () => {
    const [formData, setFormData] = useState({
        template: {
            title: "Untitled Form",
            description: "",
            topic: null,
            tags: []
        },
        questions: []
    });

    const [tagInput, setTagInput] = useState('');
    const [suggestions, setSuggestions] = useState([]);
    const [isLoading, setIsLoading] = useState(false);

    const handleTopicChange = (topicId) => {
        setFormData((prevData) => ({
            ...prevData,
            template: {
                ...prevData.template,
                topic: topicId // Set topic ID
            }
        }));
    };

    // Fetch tag suggestions from the backend
    const fetchTagSuggestions = async (query) => {
        if (!query.trim()) {
            setSuggestions([]);
            return;
        }

        setIsLoading(true);
        try {
            const token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3NDAxNDE3MDMsImV4cCI6MTc0MDIyODEwMywicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiam9uQGdtYWlsLmNvbSJ9.T9Q1e0TptlnEu1QBMICM96KAwJ94bUBZXG1uzoUQLHLC4zemZDF_9gLhvMeu5YASfTrEPhoMWd9nI_pk5LQ07Q75FzZjG56R6_-E6R5JCju8txOjBt2xhy3XP_eRWoyi3jqP-vvN0GEAcXvO36i69ooH43isckhWfM8ZH7iUdajNwjDV4GNzHe6jQ8Gs0-0mjhScnvRl8_UswnQjLX5b2xrPeJp68MtREIDPfiXWQhkPBQRlMy3htnuylHhpnuwU85Z_hdu2c7EMFMQpUS8CBe2zb5_kn6eJRaS7h1wSgWqTRQF3gP2bD1DmEmBdtwJBPHrWSx8z2VlloLmu_i1X9w";
            const response = await fetch(`http://127.0.0.1:8000/api/templates/tags/suggest?q=${encodeURIComponent(query)}`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) throw new Error('Failed to fetch suggestions');

            const data = await response.json();
            setSuggestions(data);
        } catch (error) {
            console.error('Error fetching tag suggestions:', error);
        } finally {
            setIsLoading(false);
        }
    };

    // Debounce the tag suggestions fetch
    useEffect(() => {
        const timeoutId = setTimeout(() => {
            fetchTagSuggestions(tagInput);
        }, 300);

        return () => clearTimeout(timeoutId);
    }, [tagInput]);

    const addTag = (tagName) => {
        if (!formData.template.tags.some(t => t.toLowerCase() === tagName.toLowerCase())) {
            setFormData(prev => ({
                ...prev,
                template: {
                    ...prev.template,
                    tags: [...prev.template.tags, tagName]
                }
            }));
        }
        setTagInput('');
        setSuggestions([]);
    };

    const removeTag = (tagToRemove) => {
        setFormData(prev => ({
            ...prev,
            template: {
                ...prev.template,
                tags: prev.template.tags.filter(tag => tag !== tagToRemove)
            }
        }));
    };

    const handleKeyDown = (e) => {
        if (e.key === 'Enter' && tagInput.trim()) {
            e.preventDefault();
            addTag(tagInput.trim());
        } else if (e.key === 'Backspace' && !tagInput) {
            e.preventDefault();
            const lastTag = formData.template.tags[formData.template.tags.length - 1];
            if (lastTag) {
                removeTag(lastTag);
            }
        }
    };

    const questionTypes = [
        {label: 'Short Text', value: 'text', icon: Type},
        {label: 'Radio Choice', value: 'radio', icon: Radio},
        {label: 'Checkbox', value: 'checkbox', icon: CheckSquare},
        {label: 'Dropdown', value: 'dropdown', icon: List}
    ];

    const addQuestion = (type) => {
        const newQuestion = {
            text: "New Question",
            type: type,
            required: false,
            orderNum: formData.questions.length + 1,
            options: type !== 'text' ? [
                {text: "Option 1", value: "option_1", orderNum: 1}
            ] : []
        };

        setFormData(prev => ({
            ...prev,
            questions: [...prev.questions, newQuestion]
        }));
    };

    const updateQuestion = (index, field, value) => {
        const updatedQuestions = [...formData.questions];
        updatedQuestions[index] = {
            ...updatedQuestions[index],
            [field]: value
        };
        setFormData(prev => ({...prev, questions: updatedQuestions}));
    };

    const addOption = (questionIndex) => {
        const updatedQuestions = [...formData.questions];
        const currentOptions = updatedQuestions[questionIndex].options;
        const newOption = {
            text: `Option ${currentOptions.length + 1}`,
            value: `option_${currentOptions.length + 1}`,
            orderNum: currentOptions.length + 1
        };
        updatedQuestions[questionIndex].options.push(newOption);
        setFormData(prev => ({...prev, questions: updatedQuestions}));
    };

    const updateOption = (questionIndex, optionIndex, value) => {
        const updatedQuestions = [...formData.questions];
        updatedQuestions[questionIndex].options[optionIndex].text = value;
        updatedQuestions[questionIndex].options[optionIndex].value = value.toLowerCase().replace(/\s+/g, '_');
        setFormData(prev => ({...prev, questions: updatedQuestions}));
    };

    const deleteQuestion = (index) => {
        const updatedQuestions = formData.questions.filter((_, i) => i !== index);
        setFormData(prev => ({...prev, questions: updatedQuestions}));
    };

    const deleteOption = (questionIndex, optionIndex) => {
        const updatedQuestions = [...formData.questions];
        updatedQuestions[questionIndex].options = updatedQuestions[questionIndex].options.filter((_, i) => i !== optionIndex);
        setFormData(prev => ({...prev, questions: updatedQuestions}));
    };

    const handleSave = async () => {
        try {
            const token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3NDAxNDE3MDMsImV4cCI6MTc0MDIyODEwMywicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiam9uQGdtYWlsLmNvbSJ9.T9Q1e0TptlnEu1QBMICM96KAwJ94bUBZXG1uzoUQLHLC4zemZDF_9gLhvMeu5YASfTrEPhoMWd9nI_pk5LQ07Q75FzZjG56R6_-E6R5JCju8txOjBt2xhy3XP_eRWoyi3jqP-vvN0GEAcXvO36i69ooH43isckhWfM8ZH7iUdajNwjDV4GNzHe6jQ8Gs0-0mjhScnvRl8_UswnQjLX5b2xrPeJp68MtREIDPfiXWQhkPBQRlMy3htnuylHhpnuwU85Z_hdu2c7EMFMQpUS8CBe2zb5_kn6eJRaS7h1wSgWqTRQF3gP2bD1DmEmBdtwJBPHrWSx8z2VlloLmu_i1X9w";
            const response = await fetch('http://127.0.0.1:8000/api/templates/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(formData),
            });

            if (!response.ok) {
                throw new Error('Failed to save form');
            }

            alert('Form saved successfully!');
        } catch (error) {
            alert('Error saving form: ' + error.message);
        }
    };

    return (
        <div className="min-h-screen bg-gray-50 p-6">
            <div className="max-w-4xl mx-auto">
                {/* Form Header */}
                <div className="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div className='flex flex-col sm:flex-row sm:justify-between gap-3'>
                        <input
                            type="text"
                            value={formData.template.title}
                            onChange={(e) => setFormData(prev => ({
                                ...prev,
                                template: {...prev.template, title: e.target.value}
                            }))}
                            className="w-full text-3xl font-bold mb-4 p-2 border-b border-transparent focus:border-purple-500 focus:outline-none"
                            placeholder="Form Title"
                        />
                        <TopicDropdown onTopicSelect={handleTopicChange} />
                    </div>
                    <input
                        type="text"
                        value={formData.template.description}
                        onChange={(e) => setFormData(prev => ({
                            ...prev,
                            template: {...prev.template, description: e.target.value}
                        }))}
                        className="w-full text-gray-600 p-2 border-b border-transparent focus:border-purple-500 focus:outline-none"
                        placeholder="Form Description"
                    />
                    
                </div>
                {/* Tags Section */}
                <div className="relative">
                    <div className="flex flex-wrap gap-2 mb-2">
                        {formData.template.tags.map((tag, index) => (
                            <span
                                key={index}
                                className="inline-flex items-center gap-1 px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm"
                            >
                                    {tag}
                                <button
                                    onClick={() => removeTag(tag)}
                                    className="hover:text-purple-900"
                                >
                                        <X className="w-4 h-4"/>
                                    </button>
                                </span>
                        ))}
                    </div>
                    <div className="relative">
                        <input
                            type="text"
                            value={tagInput}
                            onChange={(e) => setTagInput(e.target.value)}
                            onKeyDown={handleKeyDown}
                            className="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                            placeholder="Add tags..."
                        />
                        {isLoading && (
                            <div className="absolute right-3 top-1/2 transform -translate-y-1/2">
                                <div
                                    className="animate-spin rounded-full h-4 w-4 border-2 border-purple-500 border-t-transparent"></div>
                            </div>
                        )}
                        {suggestions.map((suggestion, index) => (
                            <button
                                key={suggestion.id}  // Use suggestion.id instead of index
                                onClick={() => addTag(suggestion.name)}  // Use suggestion.name
                                className="w-full text-left px-4 py-2 hover:bg-purple-50 focus:outline-none focus:bg-purple-50"
                            >
                                {suggestion.name}
                            </button>
                        ))}
                    </div>
                </div>

                {/* Questions */}
                <div className="space-y-4">
                    {formData.questions.map((question, questionIndex) => (
                        <div key={questionIndex} className="bg-white rounded-lg shadow-md p-6">
                            <div className="flex items-start gap-4">
                                <GripVertical className="w-6 h-6 text-gray-400 cursor-move"/>
                                <div className="flex-1">
                                    <input
                                        type="text"
                                        value={question.text}
                                        onChange={(e) => updateQuestion(questionIndex, 'text', e.target.value)}
                                        className="w-full text-lg font-medium mb-4 p-2 border-b border-transparent focus:border-purple-500 focus:outline-none"
                                        placeholder="Question text"
                                    />

                                    {question.type !== 'text' && (
                                        <div className="space-y-2 ml-6">
                                            {question.options.map((option, optionIndex) => (
                                                <div key={optionIndex} className="flex items-center gap-2">
                                                    <div className="w-4">
                                                        {question.type === 'radio' &&
                                                            <Radio className="w-4 h-4 text-gray-400"/>}
                                                        {question.type === 'checkbox' &&
                                                            <CheckSquare className="w-4 h-4 text-gray-400"/>}
                                                        {question.type === 'dropdown' &&
                                                            <List className="w-4 h-4 text-gray-400"/>}
                                                    </div>
                                                    <input
                                                        type="text"
                                                        value={option.text}
                                                        onChange={(e) => updateOption(questionIndex, optionIndex, e.target.value)}
                                                        className="flex-1 p-2 border-b border-transparent focus:border-purple-500 focus:outline-none"
                                                        placeholder="Option text"
                                                    />
                                                    <button
                                                        onClick={() => deleteOption(questionIndex, optionIndex)}
                                                        className="text-gray-400 hover:text-red-500"
                                                    >
                                                        <Trash2 className="w-4 h-4"/>
                                                    </button>
                                                </div>
                                            ))}
                                            <button
                                                onClick={() => addOption(questionIndex)}
                                                className="ml-6 text-purple-600 hover:text-purple-700 font-medium text-sm"
                                            >
                                                Add Option
                                            </button>
                                        </div>
                                    )}
                                </div>
                                <div className="flex items-center gap-2">
                                    <label className="flex items-center gap-2 text-sm text-gray-600">
                                        <input
                                            type="checkbox"
                                            checked={question.required}
                                            onChange={(e) => updateQuestion(questionIndex, 'required', e.target.checked)}
                                            className="rounded text-purple-600 focus:ring-purple-500"
                                        />
                                        Required
                                    </label>
                                    <button
                                        onClick={() => deleteQuestion(questionIndex)}
                                        className="text-gray-400 hover:text-red-500"
                                    >
                                        <Trash2 className="w-5 h-5"/>
                                    </button>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>

                {/* Add Question Button */}
                <div className="mt-6 bg-white rounded-lg shadow-md p-4">
                    <div className="flex flex-wrap gap-2">
                        {questionTypes.map((type) => (
                            <button
                                key={type.value}
                                onClick={() => addQuestion(type.value)}
                                className="flex items-center gap-2 px-4 py-2 text-purple-600 hover:bg-purple-50 rounded-md transition-colors"
                            >
                                <type.icon className="w-5 h-5"/>
                                <span>{type.label}</span>
                            </button>
                        ))}
                    </div>
                </div>

                {/* Save Button */}
                <div className="mt-6 flex justify-end">
                    <button
                        onClick={handleSave}
                        className="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors"
                    >
                        Save Form
                    </button>
                </div>
            </div>
        </div>
    )
        ;
};

export default FormBuilder;