import {ApolloClient, createHttpLink, InMemoryCache, from} from '@apollo/client/core';
import {RestLink} from 'apollo-link-rest';
import {setContext} from '@apollo/client/link/context';
import getCookie from '@/scripts/helpers/getCookie';
import EventFilterEmitter from '@/scripts/core/eventFilterEmitter';

export default new ApolloClient({
    link: from([
        setContext(() => ({
            headers: {
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'User-Agent': `Cattr-Web/v${import.meta.env.VITE_APP_VERSION}`,
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        })),
        new RestLink({
            uri: `${window.location.origin}/api/`,
            responseTransformer: async (response, typeName: string) =>
                response.status === 200 ?
                    response.json().then((e: any) => new EventFilterEmitter('rest.after').fire(typeName, e.data)) :
                    null,
        }),
        createHttpLink({
            uri: `${window.location.origin}/api/graphql`,
        }),
    ]),
    cache: new InMemoryCache(),
});
