// Views
import Home from './components/Home'
import Profile from './components/Profile'
import CategoryCreateView from './components/CategoryCreateView'

export const routes = [
    { path: '/', name: 'Home', component: Home },
    { path: '/profile', name: 'Profile', component: Profile },
	{ path: '/categories/create', name: 'Create', component: CategoryCreateView }
];