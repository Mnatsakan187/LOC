import store from '~/store'
import Cookies from 'js-cookie'

export default async (to, from, next) => {
  if(from.path == '/my-favourite'){
    Cookies.remove('collectionsPage')
  }
  if (!store.getters['auth/check']) {
    next({ name: 'login' })
  } else {
    next()
  }
}
