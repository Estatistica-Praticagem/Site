const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        component: () => import('src/pages/Route.vue'),
      },
      {
        path: '/en',
        component: () => import('src/pages/PegoraroConsulting_en.vue'),
      },
      {
        path: '/br',
        component: () => import('src/pages/PegoraroConsulting_br.vue'),
      },
      {
        path: 'login',
        component: () => import('src/pages/LoginPage.vue'),
      },
      {
        path: 'contacts',
        component: () => import('src/pages/ContactsList.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'editUser',
        component: () => import('src/pages/UserEditPage.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'registerUser',
        component: () => import('src/pages/UserRegisterPage.vue'),
        meta: { requiresAuth: true },
      },
    ],
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
];

export default routes;
