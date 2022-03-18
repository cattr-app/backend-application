import {defineComponent, h, resolveComponent} from 'vue';
import {string} from 'yup';
import {render} from '@/scripts/core/elements/form';
import {DefineComponent} from '@vue/runtime-core';
import {CattrFormFieldTypes} from '@/scripts/components/fieldList';
import loginAction from '@/scripts/graphql/mutations/auth/login';

export default defineComponent({
    name: 'auth.login',
    render() {
        return render(
            'core.auth.login',
            {
                email: {
                    placeholder: this.$t('auth.email'),
                    as: CattrFormFieldTypes.Email,
                    order: 10,
                    rules: string().email().required(),
                },
                password: {
                    placeholder: this.$t('auth.password'),
                    as: CattrFormFieldTypes.Password,
                    order: 10,
                    rules: string().required(),
                },
                submit: {
                    order: 100,
                    label: this.$t('auth.login'),
                    as: CattrFormFieldTypes.Button,
                    button: {
                        action: 'submit',
                        expanded: true,
                    },
                },
                emailLogin: {
                    order: 110,
                    render: () =>
                        h(
                            'div',
                            {class: 'text-center'},
                            [
                                h(
                                    resolveComponent('router-link') as DefineComponent,
                                    {to: {name: 'auth.email'}},
                                    {default: () => this.$t('auth.emailLogin')}),
                            ],
                        ),
                },
            },
            loginAction,
        );
    },
});
