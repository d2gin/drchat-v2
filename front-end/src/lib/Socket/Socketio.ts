import {inject, ref, unref} from 'vue';
import {io} from "socket.io-client";
import {Socket} from "socket.io-client/build/esm/socket";
import Config from "@/lib/Config";
import EventBus, {Event, EventList} from "@/lib/EventBus";

export const useSocketio = (): Socketio => <Socketio>inject('socketio');

export class Socketio {
    public io!: Socket;
    protected eventBus: EventBus;
    protected defaultEvents: EventList = {
        connect: [
            {
                event: () => {
                    console.log('connected');
                },
                once: false,
            },
        ],
        disconnect: [
            {
                event: () => {
                    console.log("disconnected");
                },
                once: false,
            },
        ],
    };

    constructor() {
        this.eventBus = new EventBus();
    }

    public connect(options = {}) {
        if (this.io?.connected) {
            return true;
        }
        this.io = io(Config.websocket, {
            ...options,
            transports: ['websocket'],
            autoConnect: false,
        });
        this.listen();
        this.io.connect();
        return this;
    }

    public disconnect() {
        if (this.io?.connected) {
            this.eventBus.offAny();
            this.io.disconnect();
        }
    }

    public isConnect() {
        return this.io?.connected;
    }

    /**
     * 监听事件
     */
    public listen() {
        // 重置所有默认队列
        for (let event in this.defaultEvents) {
            this.defaultEvents[event].forEach((item: Event) => {
                // 置入最前
                this.eventBus.off(event, item.event).on(event, item.event, item.once, true);
            });
        }
        // websocket监听
        if (this.io) {
            this.io.off();
            for (let event in this.eventBus.getEventList()) {
                this.io.on(event, (...args: any[]) => this.eventBus.trigger(event, ...args));
            }
        }
        return this;
    }

    /**
     * 卸载事件队列
     * @param name
     * @param resolver
     */
    public off(name: string, resolver?: Function) {
        this.eventBus.off(name, resolver);
        return this.listen();
    }

    /**
     * 监听事件队列
     * @param name
     * @param event
     * @param once
     * @param unshift
     */
    public on(name: string, event: Function, once: boolean = false, unshift: boolean = false) {
        if (this.io?.connected && name == 'connect') {
            event.apply(null);
        }
        this.eventBus.on(name, event, once, unshift);
        return this.listen();
    }

    public onDefault(name: string, event: Function, once: boolean = false) {
        let payload: Event = {event, once};
        if (name in this.defaultEvents) {
            this.defaultEvents[name].push(payload);
        } else {
            this.defaultEvents[name] = [payload];
        }
        return this.on(name, event, once, true);
    }

    public emit(event: string, ...args: any[]) {
        return this.io.volatile.emit(event, ...args);
    }
}

export default new Socketio();
