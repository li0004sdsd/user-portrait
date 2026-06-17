import client from './client'
export const getCategories = () => client.get('/tag-categories')
export const createCategory = data => client.post('/tag-categories', data)
export const updateCategory = (id, data) => client.put(`/tag-categories/${id}`, data)
export const deleteCategory = id => client.delete(`/tag-categories/${id}`)
export const getTags = () => client.get('/tags')
export const createTag = data => client.post('/tags', data)
export const updateTag = (id, data) => client.put(`/tags/${id}`, data)
export const deleteTag = id => client.delete(`/tags/${id}`)
