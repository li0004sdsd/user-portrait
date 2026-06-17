import client from './client'
export const login = data => client.post('/login', data)
export const register = data => client.post('/register', data)
export const logout = () => client.post('/logout')
export const getProfile = () => client.get('/profile')
export const updateProfile = data => client.put('/profile', data)
