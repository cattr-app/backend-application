import {defineComponent, h, PropType, reactive} from 'vue';
import {useField, useForm} from 'vee-validate';
import {DocumentNode} from 'graphql';
import {useMutation} from '@vue/apollo-composable';
import {FetchResult} from '@apollo/client';
import fieldList, {CattrFormFieldTypes, ICattrFormField} from '@/scripts/components/fieldList';
import sha512 from '@/scripts/helpers/sha512';

const createValues = (schema: ICattrFormSchemaContainer) => {
    const formData: any = reactive({});
    const formErrors: any = reactive({});

    Object.keys(schema).forEach(key => {
        if (schema[key].as === CattrFormFieldTypes.Button || !schema[key].as)
            return;

        if (schema[key].as === CattrFormFieldTypes.Merge || schema[key].as === CattrFormFieldTypes.Group) {
            const data = createValues(schema[key].fields!);

            formData[key] = data.formData;
            formErrors[key] = data.formErrors;
        } else {
            const {value, errorMessage} = useField(key, schema[key].rules);

            formData[key] = value;
            formErrors[key] = errorMessage;
        }
    });

    return {formData, formErrors};
};

export default defineComponent({
    name: 'cattrForm',
    setup(props) {
        const {validate} = useForm();

        const {formData, formErrors} = createValues(props.schema);

        const {mutate: submitMutation, loading} = useMutation(props.mutation);

        return {
            validate,
            formData,
            formErrors,
            submitMutation,
            loading,
        };
    },
    render() {
        return h('form', {
            onSubmit: (event: SubmitEvent) => {
                event.preventDefault();

                this.validate().then(async (result) => {
                    if (!result.valid)
                        return;

                    const mutatedData: any = {};

                    for (const formDataKey in this.formData) {
                        mutatedData[formDataKey] = this.formData[formDataKey];

                        if(Object.prototype.hasOwnProperty.call(this.schema[formDataKey], 'mutateBeforeSend')){
                            // @ts-ignore
                            mutatedData[formDataKey] = this.schema[formDataKey].mutateBeforeSend(mutatedData[formDataKey]);
                        }

                        if(this.schema[formDataKey].as === CattrFormFieldTypes.Password){
                            mutatedData[formDataKey] = await sha512(mutatedData[formDataKey]);
                        }
                    }

                    this.submitMutation({formData: mutatedData}).then(this.actions?.onResult, this.actions?.onError);
                });
            },
        }, [
            h(fieldList, {
                schema: this.schema, fieldData: this.formData, fieldErrors: this.formErrors, loading: this.loading,
                onUpdate: ($event: any) => this.formData[$event.key] = $event.value,
            }),
        ]);
    }, props: {
        schema: {
            type: Object as PropType<ICattrFormSchemaContainer>,
            required: true,
        },
        mutation: {
            type: Object as PropType<DocumentNode>,
            required: true,
        },
        actions: {
            type: Object as PropType<ICattrFormActions>,
            default: {},
        },
    },
});

export interface ICattrFormActions {
    onResult?: (value: (FetchResult<any> | null)) => (void | PromiseLike<void>),
    onError?: (reason: any) => (void | PromiseLike<void>),
}

export interface ICattrFormSchemaContainer {
    [name: string]: ICattrFormField;
}
