import {createStore} from 'vuex'
import User from "@/store/modules/User";
import Client from "@/store/modules/Client";
import Conversation from "@/store/modules/Conversation";
import Contact from './modules/Contact';
import Chat from './modules/Chat';

export default createStore({
    state: {},
    getters: {},
    mutations: {},
    actions: {},
    modules: {
        User,
        Client,
        Conversation,
        Contact,
        Chat,
    }
})
