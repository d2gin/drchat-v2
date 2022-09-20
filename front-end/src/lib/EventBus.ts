export interface Event {
    event: Function,
    once: boolean,
}

export interface EventList {
    [key: string]: Event[]
}

export default class EventBus {

    protected events: EventList = {};

    /**
     * 触发事件
     * @param event
     * @param args
     */
    public trigger(event: string, ...args: any[]) {
        let listens: Event[] = this.events[event] || [];
        listens.forEach((item: Event, index: number) => {
            let cb: Function = item.event;
            try {
                cb.apply(null, args);
            } catch (e) {
                console.log(e);
            } finally {
                if (item.once) {
                    delete this.events[event][index];
                }
            }
        });
    }

    /**
     * 卸载事件队列
     * @param name
     * @param resolver
     */
    public off(name: string = '', resolver?: Function) {
        if (name && resolver) {
            this.events[name]?.forEach((item: Event, index) => {
                if (item.event === resolver) {
                    delete this.events[name][index];
                }
            });
        } else if (name) {
            delete this.events[name];
        } else {
            this.events = {};
        }
        return this;
    }

    public offAny() {
        this.events = {};
    }

    /**
     * 监听事件队列
     * @param name
     * @param event
     * @param once
     * @param unshift
     */
    public on(name: string, event: Function, once: boolean = false, unshift: boolean = false) {
        const payload: Event = {event, once,};
        if (this.events.hasOwnProperty(name) && unshift) {
            this.events[name].unshift(payload);
        } else if (this.events.hasOwnProperty(name) && !unshift) {
            this.events[name].push(payload);
        } else {
            this.events[name] = [payload,];
        }
        return this;
    }

    /**
     * 订阅
     * @param name
     * @param queue
     */
    public subcribe(name: string, queue: Event[]) {
        queue.forEach((item: Event) => this.on(name, item.event, item.once));
    }

    public getEventList() {
        return {...this.events};
    }
}
