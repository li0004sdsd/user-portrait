import client from './client'
export const getPortraits = (page = 1) => client.get(`/portraits?page=${page}`)
export const getPortrait = id => client.get(`/portraits/${id}`)
export const createPortrait = data => client.post('/portraits', data)
export const updatePortrait = (id, data) => client.put(`/portraits/${id}`, data)
export const deletePortrait = id => client.delete(`/portraits/${id}`)
