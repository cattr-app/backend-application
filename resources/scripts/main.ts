import {createApp} from 'vue'
import {i18nVue} from 'laravel-vue-i18n'
import Oruga from '@oruga-ui/oruga-next'
import layoutsLoader from '@/scripts/plugins/layoutsLoader';
import store from '@/scripts/store';
import router from '@/scripts/router';
import app from '@/scripts/app';
//import '@oruga-ui/oruga-next/src/scss/oruga.scss';

createApp(app)
  .use(store)
  .use(router)
  .use(i18nVue, {
    resolve: async lang => {
      const langs = import.meta.glob('/resources/lang/*.json');
      return await langs[`/resources/lang/${lang}.json`]();
    }
  })
  .use(layoutsLoader)
  .use(Oruga)
  .mount('#app');
