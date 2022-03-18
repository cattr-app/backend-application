import {ICattrFormSchemaContainer} from '@/scripts/components/form';
import EventFilterEmitter from '@/scripts/core/eventFilterEmitter';

export default class Module {
    extendFormSchema(formName: string, schema: ICattrFormSchemaContainer): void {
        new EventFilterEmitter('form.schema').on(formName, (data) => {
            console.log(data);
        });
    }
}
