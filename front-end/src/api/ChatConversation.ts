import {Http} from "@/lib/Http";

export class ChatConversation extends Http {
    CONVERSATION_NORMAL = 1;// 私聊
    CONVERSATION_GROUP = 2;// 群聊
    list(page = 1, limit = 50) {
        return this.get('/api/chat/conversation/list', {page, limit});
    }

    hintCount() {
        return this.get('/api/chat/conversation/hintCount');
    }

    createConversation(receiver: number, isGroup = false) {
        return this.post(`/api/chat/conversation/createConversation${isGroup ? '/group' : '/normal'}`, {receiver});
    }

    clearUnread(receiver: number, isGroup = false) {
        return this.post(`/api/chat/conversation/clearUnread${isGroup ? '/group' : '/normal'}`, {receiver});
    }
}

export default new ChatConversation();
