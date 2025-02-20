import React, { useState } from 'react';
import Nav from '../../components/navbar/Nav';
import Recent from '../../components/recent/Recent';
import Hero from '../../components/hero/Hero';
import Popular from '../../components/popular/Popular';
import TagCloud from '../../components/tagCloud/TagCloud';

const Home = () => {
    const [searchQuery, setSearchQuery] = useState('');

    return (
        <div className="min-h-screen bg-gray-50">
            <Nav/>
            <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <Hero/>
                <Recent/>
                <Popular/>
                <TagCloud/>
            </main>
        </div>
    );
};

export default Home;