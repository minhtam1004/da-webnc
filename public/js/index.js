// Views
import Home from './components/Home'
import CategoryCreateView from './components/CategoryCreateView'

export const routes = [
	{ path: '/', name: 'Category', component: Home },
	{ path: '/categories/create', name: 'Create', component: CategoryCreateView }
];