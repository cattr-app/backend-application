import {h, VNode} from 'vue';
import {DocumentNode} from 'graphql';
import form, {ICattrFormSchemaContainer} from '@/scripts/components/form';
import EventFilterEmitter from '@/scripts/core/eventFilterEmitter';

export function render(name: string, schema: ICattrFormSchemaContainer, mutation: DocumentNode): VNode {
    return h(form, {
        name,
        schema: new EventFilterEmitter('form.schema').fire(name, schema),
        mutation: new EventFilterEmitter('form.mutation').fire(name, mutation),
    });
}
