import { route } from 'quasar/wrappers';
import {
  createRouter,
  createMemoryHistory,
  createWebHistory,
  createWebHashHistory,
} from 'vue-router';
import routes from './routes';

export default route((/* { store, ssrContext } */) => {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : (process.env.VUE_ROUTER_MODE === 'history'
      ? createWebHistory
      : createWebHashHistory);

  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,
    history: createHistory(process.env.VUE_ROUTER_BASE),
  });

  // ✅ Proteção de rotas com login
  Router.beforeEach((to, from, next) => {
    const isLoggedIn = !!localStorage.getItem('user_id');

    if (to.meta.requiresAuth && !isLoggedIn) {
      next('/login'); // redireciona se não tiver logado
    } else {
      next(); // segue normalmente
    }
  });

  return Router;
});
