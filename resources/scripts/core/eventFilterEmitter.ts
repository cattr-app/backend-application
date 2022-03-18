interface EventContainer {
    [eventName: string]: {
        [object: string]: [
            (...args: any[]) => any
        ],
    };
}

const events: EventContainer = {};

const isFunction = (fn: any): fn is (...args: any[]) => any => typeof fn === 'function';

export default class EventFilterEmitter {
    constructor(private readonly filterName: string) {}

    on(object: string, listener: (...args: any[]) => any): this {
        events[this.filterName][object].push(listener);

        return this;
    }

    prependListener(object: string, listener: (...args: any[]) => any): this {
        events[this.filterName][object].unshift(listener);

        return this;
    }

    fire<T>(object: string, data: T, ...args: any[]): T {
        if (events.hasOwnProperty(this.filterName)) {
            if (events[this.filterName].hasOwnProperty('*')) {
                for (const handler in events[this.filterName]['*']) {
                    if (isFunction(handler)) {
                        const returnData = handler(data, args);
                        if (returnData)
                            data = returnData;
                    }
                }
            }
            for (const handler in events[this.filterName][object]) {
                if (isFunction(handler)) {
                    const returnData = handler(data, args);
                    if (returnData)
                        data = returnData;
                }
            }
        }

        return data;
    }
}
