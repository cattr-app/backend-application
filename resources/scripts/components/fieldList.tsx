import {defineComponent, h, PropType, resolveComponent, VNode} from 'vue';
import {DefineComponent} from '@vue/runtime-core';
import {AnySchema} from 'yup';
import {ICattrFormSchemaContainer} from '@/scripts/components/form';

const fieldList = defineComponent({
    name: 'cattrFieldList',
    render() {
        const render = (name: string) => {
            let innerField: any;

            if (this.schema[name].render)
                innerField = this.schema[name].render!();
            else
                switch (this.schema[name].as) {
                    case CattrFormFieldTypes.Input:
                        innerField = h(resolveComponent('o-input') as DefineComponent, {
                            placeholder: this.schema[name].placeholder,
                            modelValue: this.fieldData[name],
                            onInput: (element: InputEvent) => this.$emit('update', {
                                key: name,
                                value: (element.target as HTMLInputElement)?.value,
                            }),
                        });
                        break;
                    case CattrFormFieldTypes.Password:
                        innerField = h(resolveComponent('o-input') as DefineComponent, {
                            placeholder: this.schema[name].placeholder,
                            type: 'password',
                            passwordReveal: true,
                            class: 'password',
                            modelValue: this.fieldData[name],
                            onInput: (element: InputEvent) => this.$emit('update', {
                                key: name,
                                value: (element.target as HTMLInputElement)?.value,
                            }),
                        });
                        break;
                    case CattrFormFieldTypes.Email:
                        innerField = h(resolveComponent('o-input') as DefineComponent, {
                            placeholder: this.schema[name].placeholder,
                            modelValue: this.fieldData[name],
                            type: 'email',
                            onInput: (element: InputEvent) => this.$emit('update', {
                                key: name,
                                value: (element.target as HTMLInputElement)?.value,
                            }),
                        });
                        break;
                    case CattrFormFieldTypes.Merge:
                    case CattrFormFieldTypes.Group:
                        // @ts-ignore
                        innerField = h(fieldList, {
                            wrap: false,
                            loading: this.loading,
                            schema: this.schema.fields!,
                            fieldData: this.fieldData[name],
                            fieldErrors: this.fieldErrors[name],
                            onUpdate: ($event: any) => {this.fieldData[name][$event.key] = $event.value; this.$emit('update', {key: name, value: this.fieldData[name]})}
                        });
                        break;
                    case CattrFormFieldTypes.Button:
                        innerField = h(resolveComponent('o-button') as DefineComponent, {
                            label: this.schema[name].button?.action !== 'submit' || !this.loading ? this.schema[name].label : '',
                            expanded: this.schema[name].button?.expanded,
                            outlined: this.schema[name].button?.outlined,
                            inverted: this.schema[name].button?.inverted,
                            variant: this.schema[name].button?.variant,
                            nativeType: this.schema[name].button?.action === 'submit' ? 'submit' : 'button',
                            tag: this.schema[name].button?.action === 'link' ? 'router-link' : 'button',
                            to: this.schema[name].button?.to,
                            onClick: typeof this.schema[name].button?.action !== 'string' ? this.schema[name].button?.action : null,
                            iconLeft: this.schema[name].button?.icon,
                            iconRight: this.schema[name].button?.action === 'submit' && this.loading ? 'loading' : '',
                            iconRightClass: 'o-icon--spin',
                        });
                        break;
                    default:
                        throw new Error('Unknown field requested for form');
                }

            return innerField;
        };


        return Object.keys(this.schema).sort((keyA, keyB) => {
            return this.schema[keyA].order < this.schema[keyB].order ? -1 : this.schema[keyA].order > this.schema[keyB].order ? 1 : 0;
        }).map(key => this.wrap ? h(resolveComponent('o-field') as DefineComponent,
            {
                label: this.schema[key].as !== CattrFormFieldTypes.Button ? this.schema[key].label : '',
                message: (typeof this.fieldErrors[key] === 'object') ? Object.values(this.fieldErrors[key] as {}).reduce((acc: string, el) => el ? acc + el : acc, '') : this.fieldErrors[key],
                variant: (typeof this.fieldErrors[key] === 'object') ? Object.values(this.fieldErrors[key] as {}).reduce((acc: string, el) => el ? acc + el : acc, '') : this.fieldErrors[key] ? 'danger' : null,
                grouped: this.schema[key].as === CattrFormFieldTypes.Group,
            }, {
                default: () => render(key),
            }) : render(key));
    },
    emits: ['update'],
    props: {
        wrap: {
            type: Boolean,
            default: true,
        },
        schema: {
            type: Object as PropType<ICattrFormSchemaContainer>,
            required: true,
        },
        loading: {
            type: Boolean,
            default: false,
        },
        fieldData: {
            type: Object,
            required: true,
        },
        fieldErrors: {
            type: Object,
            required: true,
        },
    },
});


export default fieldList;

export const enum CattrFormFieldTypes {
    Input,
    Password,
    Email,
    Select,
    Date,
    DateTime,
    Time,
    Group,
    Merge,
    Upload,
    Checkbox,
    Switch,
    Button
}

const buttonActions = ['submit', 'link'] as const;
const tooltipPositions = ['top', 'bottom', 'left', 'right'] as const;

export interface ICattrFormField {
    as?: CattrFormFieldTypes,
    label?: string,
    placeholder?: string,
    order: number,
    render?: () => VNode,
    type?: string,
    rules?: AnySchema,
    fields?: ICattrFormSchemaContainer,
    field?: ICattrFormField,
    tooltip?: {
        position?: typeof tooltipPositions[number],
        variant?: string,
    },
    button?: {
        action?: Function | typeof buttonActions[number],
        expanded?: boolean,
        outlined?: boolean,
        inverted?: boolean,
        variant?: string,
        icon?: string,
        to?: string,
    },
    mutateBeforeSend?: (value: any) => any,
}
