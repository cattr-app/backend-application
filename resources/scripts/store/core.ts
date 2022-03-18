import {defineStore} from 'pinia';

const notifyType = ['toast', 'notify'] as const;

export default defineStore('core', {
    state: () => ({
        token: null,
        uiLoadLocksCounter: 1,
        networkError: false,
    }),
    actions: {
        lockUi() {
            this.uiLoadLocksCounter++;
        },
        unlockUi() {
            this.uiLoadLocksCounter--;
        },
    },
    persist: {
        paths: ['token'],
    },
});
