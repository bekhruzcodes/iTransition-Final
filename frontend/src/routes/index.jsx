import React from 'react'
import App from '../App'
import Login from './Auth/Login/Login'
import Register from './Auth/Register/Register'
import { useRoutes } from 'react-router-dom'
import Dashboard from './home/Home'
import CreateTemplate from '../pages/CreateTemplate'
import SinglePage from './singlePage/SinglePage'

const RouteController = () => {
    return useRoutes([
        {
            path: '',
            element: <Dashboard/>
        },
        {
            path: 'login',
            element: <Login/>
        },
        {
            path: 'register',
            element: <Register/>
        },
        
        {
            path: 'create-template',
            element: <CreateTemplate/>
        },
        {
            path: 'templates/:id',
            element: <SinglePage/>
        }
    ])
}

export default RouteController