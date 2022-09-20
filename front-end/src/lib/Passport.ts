import cookie from "js-cookie";
import store from "@/store";

export class Passport {
    token_key = 'drchat_token';

    getToken(): string {
        return cookie.get(this.token_key) as string;
    }

    setToken(token: string) {
        cookie.set(this.token_key, token);
    }

    isLogin(): boolean {
        return this.getToken()?.length > 0;
    }

    logout() {
        this.setToken('');
        store.commit('User/setInfo', null);
        store.commit('User/setToken', null);
        store.commit('Contact/setFriendList', []);
        store.commit('Contact/setGroupList', []);
        store.commit('Contact/setHintCount', 0);
        store.commit('Conversation/setConversationList', []);
        store.commit('Conversation/setHintCount', 0);
        return true;
    }
}


export default new Passport();
