import Vuex from 'vuex'
import VuexPersistence from 'vuex-persist'
// import createPersistedState from 'vuex-persistedstate'

export default Vuex.createStore({
    state: {
        flashes: [],
        lastFlashId: 0,
    },
    mutations: {
        addFlash(state, { message, type }) {
            state.flashes.push({ id: ++state.lastFlashId, message: message, type: type })
        },
        deleteFlash(state, id) {
            state.flashes = state.flashes.filter(f => f.id != id)
        },
    },
})

export const persistentStore = Vuex.createStore({
    storage: window.localStorage,
    state: {
        sessionExpiryInSeconds: 0,
    },
    mutations: {
        setSessionExpiry(state, timeInSeconds) {
            state.sessionExpiryInSeconds = timeInSeconds
        },
        invalidateSession(state) {
            state.sessionExpiryInSeconds = (new Date()).getTime() / 1000 - 1
        },
    },
    plugins: [new VuexPersistence().plugin],
})

export const updateSessionFromResponse = (response) => {
    if (response !== undefined && response.data !== undefined && typeof response.data.sessionExpiry === 'number') {
        persistentStore.commit('setSessionExpiry', response.data.sessionExpiry)
    }
}