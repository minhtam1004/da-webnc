// Views
import Home from './components/Home'
import Profile from './components/Profile'
import Login from './components/Login'
import Register from './components/Register'
import AuthContainer from "./components/AuthContainer"
import CategoryCreateView from './components/CategoryCreateView'

export const routes = [
    { path: '/', name: 'Home', component: Home },
    { path: '/profile', name: 'Profile', component: Profile },
    { path: '/categories/create', name: 'Create', component: CategoryCreateView },
    {
        path: '', component: AuthContainer,
        children: [
          { path: '/login', name: 'Login', component: Login },
          { path: '/register', name: 'Register', component: Register }
        ]
      },
];