import choosePlan from './../shared/payment/choose-plan';
function page (path) {
  return () => import(/* webpackChunkName: '' */ `~/pages/${path}`).then(m => m.default || m)
}

export default [
  { path: '*', name: '404', component: page('errors/404.vue') },
  { path: '/', name: 'welcome', component: page('welcome.vue') },
  { path: '/login', name: 'login', component: page('login/login.vue') },
  { path: '/register-user', name: 'register.user', component: page('register/register-user.vue') },
  { path: '/register-creator', name: 'register.creator', component: page('register/register-creator.vue') },
  { path: '/password/reset', name: 'password.request', component: page('login/resetPassword/email.vue') },
  { path: '/password/reset/:token', name: 'password.reset', component: page('login/resetPassword/reset.vue') },
  { path: '/email/verify/:id', name: 'verification.verify', component: page('login/verification/verify.vue') },
  { path: '/email/resend', name: 'verification.resend', component: page('login/verification/resend.vue') },
  { path: '/home', name: 'home', component: page('home.vue') },
  { path: '/verify-email/:token', name: 'verify.email', component: page('login/verification/verifiedEmailAddress.vue') },
  { path: '/settings', name: 'settings', component: page('settings/settings.vue') },
  { path: '/profiles', name: 'profile.index', component: page('profile/index.vue') },
  { path: '/profiles/create', name: 'profile.create', component: page('profile/Form.vue') },
  { path: '/profiles/edit/:id', name: 'profile.edit', component: page('profile/Form.vue') },
  { path: '/projects/create',   name: 'project.create', component: page('project/Form.vue') },
  { path: '/project/edit/:projectId', name: 'project.edit', component: page('project/Form.vue') },
  { path: '/posts/create', name: 'post.create', component: page('posts/Form.vue') },
  { path: '/post/edit/:postId', name: 'post.edit', component: page('posts/Form.vue') },
  { path: '/post/post-detail/:postId', name: 'post.view', component: page('posts/postView.vue') },
  { path: '/post/post-detail/:postId/:notificationId', name: 'post.view.notification', component: page('posts/postView.vue') },
  { path: '/groups/create', name: 'group.create', component: page('group/Form.vue') },
  { path: '/groups/edit/:id', name: 'group.edit', component: page('group/Form.vue') },
  { path: '/groups', name: 'group.index', component: page('group/GroupList.vue') },
  { path: '/groups/:id', name: 'group.view', component: page('group/GroupDetail.vue') },
  { path: '/groups/:id/members', name: 'group.members', component: page('group/AddGroupMembers.vue') },
  { path: '/choose/plan', name: 'choose.plan', component: choosePlan },
  { path: '/polls/create', name: 'poll.create', component: page('polls/Form.vue') },
  { path: '/polls/edit/:pollId', name: 'poll.edit', component: page('polls/Form.vue') },
  { path: '/polls/poll-detail/:pollId', name: 'poll.view', component: page('polls/pollView.vue') },
  { path: '/polls/poll-detail/:pollId/:notificationId', name: 'poll.view.notification', component: page('polls/pollView.vue') },
  { path: '/events/create', name: 'event.create', component: page('events/event-content.vue') },
  { path: '/event/:profileId/edit/:eventId', name: 'event.edit', component: page('events/edit.vue') },
  { path: '/calendar', name: 'calendar', component: page('calendar/calendar.vue') },
  { path: '/collections/create', name: 'collection.create', component: page('collections/Form.vue') },
  { path: '/collection/edit/:collectionId', name: 'collection.edit', component: page('collections/Form.vue') },
  { path: '/events/map', name: 'events.map', component: page('map/events-map.vue') },
  { path: '/events/list', name: 'events.list', component: page('events-list/events.vue') },
  { path: '/events/posters', name: 'events.posters', component: page('events-list/posters.vue') },
  { path: '/notifications', name: 'user.notifications', component: page('notifications/notifications.vue') },
  { path: '/project-detail/project/:projectId', name: 'project.detail', component: page('project/ProjectDetail.vue') },
  { path: '/project-detail/project/:projectId/:notificationId', name: 'project.detail.notification', component: page('project/ProjectDetail.vue') },
  { path: '/project-detail/project/:projectId/project/info', name: 'project.info', component: page('project/ProjectInfoMobile.vue') },
  { path: '/project-detail/project/:projectId/project/team', name: 'project.team', component: page('project/ProjectTeamMobile.vue') },
  { path: '/project-media/:projectId/project/edit/:id', name: 'project.media', component: page('projects/media-edit.vue') },
  { path: '/profile/team/:id', name: 'profile.team', component: page('profile/AddProfileTeamMembers.vue') },
  { path: '/project/team/:id', name: 'project.create.team', component: page('project/AddProjectTeamMembers.vue') },
  { path: '/messages', name: 'messages', component: page('messages/messages.vue') },
  { path: '/networks', name: 'networks', component: page('networks/networks.vue') },
  { path: '/profile-detail/:profileId/:notificationId', name: 'profile.detail.notification', component: page('profile/ProfileDetail.vue') },
  { path: '/not-found/:notificationId', name: 'not-found', component: page('notifications/NotFoundNotification.vue') },

  { path: '/hud', component: page('hud/hud.vue'), name: 'hud',
    children: [
      {
        name: 'my.feed',
        path: '/my-feed',
        component: page('hud/my-feed.vue')
      },
      {
        name: 'discover',
        path: '/discover',
        component: page('hud/discover.vue')
      },
    ]
  },

  {
    path: '/search',
    name: 'search',
    component: page('search/search.vue'),
    children: [
      {
        name: 'searchsplitted',
        path: '/searchsplitted',
        component: page('search/searchSplitted.vue')
      },
      {
        name: 'searchSingle',
        path: '/searchsingle',
        component: page('search/searchSingle.vue'),
        props: true
      }
    ]
  },

  {
    path: '/my-favourite',
    name: 'my.favourite',
    component:page('favourite/MyFavouriteContent.vue'),
    children: [
      {
      name: 'my.fav.creators',
      path: 'creators',
      component: page('favourite/Creators.vue'),
      },
      {
        name: 'my.fav.projects',
        path: 'projects',
        component:page('favourite/Projects.vue'),
      },
      {
        name: 'my.fav.collections',
        path: 'collections',
        component:page('favourite/Collections.vue'),
      }
    ]
  },

  {
    name: 'collection.detail',
    path: '/my-favourite/collection-detail/:id',
    props: true,
    component: page('favourite/CollectionDetail.vue')
  },


  {
    path: '/profile-detail/:profileId',
    component:  page('profile/ProfileDetail.vue'),
    children: [{
      name: 'ProfileNews',
      path: '',
      component: page('profile/ProfileNews.vue')
    },
      {
        name: 'profile.projects',
        path: 'profile/projects',
        component: page('profile/ProfileProjects.vue')
      }
    ]
  },


  {
    name: 'profile.info.mobile',
    path: '/profile-detail/:profileId/profile/info',
    component: page('profile/ProfileInfoMobile.vue')
  },
  {
    path: '/profile-detail/:profileId/profile/team',
    name: 'ProfileTeamMobile',
    component: page('profile/ProfileTeamMobile.vue')
  },


  {
    path: '/analytics',
    name: 'analytics',
    component: page('analytics/Analytics.vue'),
    children: [{
      name: 'AnalyticsFollowers',
      path: 'followers',
      component: page('analytics/Followers.vue')
    },
      {
        name: 'AnalyticsProjects',
        path: 'projects',
        component: page('analytics/Projects.vue')
      },
      {
        name: 'AnalyticsPolls',
        path: 'polls',
        component: page('analytics/Polls.vue')
      }
    ]
  },

]
