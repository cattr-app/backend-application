import {defineComponent, h, provide, resolveComponent} from 'vue';
import {DefineComponent} from '@vue/runtime-core';
import {DefaultApolloClient, provideApolloClient, useQuery} from '@vue/apollo-composable';
import store from '@/scripts/store/core';
import apolloClient from '@/scripts/services/apollo';
import statusQuery from '@/scripts/graphql/queries/status';
import networkErrorView from '@/scripts/views/error/networkError';

const defaultLayout = 'default-layout';

export default defineComponent({
    setup() {
        const coreStorage = store();

        coreStorage.$subscribe((mutation, state) => {
            if (state.uiLoadLocksCounter <= 0) {
                setTimeout(() => {
                    const loader = window.document.getElementById('loader');

                    if (!loader)
                        return;

                    loader.classList.add('done');
                    setTimeout(() => {
                        loader.remove();
                    }, 1000);
                }, 1000);
            }
        });

        coreStorage.lockUi();

        provide(DefaultApolloClient, apolloClient);

        provideApolloClient(apolloClient);

        useQuery(statusQuery, null, {
            fetchPolicy: 'network-only',
        });

        return {
            coreStorage,
        };
    },
    render() {
        return h(
                resolveComponent(this.layout) as DefineComponent,
                {
                    onVnodeMounted: () => this.coreStorage.unlockUi(),
                },
                {
                    default: () => h(resolveComponent('router-view')),
                },
            );
    },
    data: () => ({
        layout: defaultLayout,
    }),
    watch: {
        $route: {
            immediate: true,
            handler(route) {
                this.layout = route.meta.layout ? `${route.meta.layout}-layout` : defaultLayout;
            },
        },
    },
});
