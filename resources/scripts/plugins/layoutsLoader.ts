import {App} from "vue";

export default {
    install(app: App) {
        Object.values(import.meta.globEager('/resources/scripts/layouts/*.vue')).forEach(e => {
            app.component(e.default.name, e.default);
        })
    },
}
