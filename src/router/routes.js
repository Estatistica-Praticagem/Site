const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        // component: () => import('pages/IndexPage.vue'),
        component: () => import('src/pages/PegoraroConsulting.vue'),
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
