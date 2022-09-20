import {Http} from "@/lib/Http";

export class Chat extends Http {

    send(send_to: number, content: string, is_group = false) {
        return this.post(`/api/chat/send${is_group ? '/group' : '/normal'}`, {send_to, content});
    }

    records(conversation_id = 0, page = 1, limit = 30) {
        return this.get(`/api/chat/records`, {conversation_id, page, limit});
    }

    expressions() {
        return this.get('/api/chat/expressions');
    }
}

export default new Chat();
