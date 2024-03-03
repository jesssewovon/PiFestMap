import axios from "axios"

export const getProducts = ({commit}, data) => {
	axios.get('/api/v1/products', data)
	.then(res => {
		commit('SET_PRODUCT', res.data)
	})
}