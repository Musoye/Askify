import Cookies from 'js-cookie'

export const setAuthToken = (token) => {
  Cookies.set('token', token, { expires: 7 }) // 7 days
}

export const getAuthToken = () => {
  return Cookies.get('token')
}

export const removeAuthToken = () => {
  Cookies.remove('token')
}

export const setUserData = (userData) => {
  Cookies.set('user', JSON.stringify(userData), { expires: 7 })
}

export const getUserData = () => {
  const userData = Cookies.get('user')
  return userData ? JSON.parse(userData) : null
}

export const removeUserData = () => {
  Cookies.remove('user')
}

export const isAuthenticated = () => {
  return !!getAuthToken()
}

export const isAdmin = () => {
  const userData = getUserData()
  return userData && userData.role === 'admin'
}
