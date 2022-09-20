import {Http} from "@/lib/Http";

export class Group extends Http {
    /**
     * 群聊列表
     * @param page
     * @param limit
     */
    public list(page: number = 1, limit: number = 200) {
        return this.get(`/api/chat/group/list?page=${page}`);
    }

    /**
     * 创建群聊
     * @param group_name
     * @param members
     */
    public create(group_name: string, members: number[] = []) {
        return this.post('/api/chat/group/create', {group_name, members});
    }

    /**
     * 群成员
     * @param group_id
     * @param page
     * @param limit
     */
    public members(group_id: number, page = 1, limit = 100) {
        return this.get('/api/chat/group/members', {group_id, page, limit});
    }

    /**
     * 加入群聊
     * @param group_id
     */
    public join(group_id: number) {
        return this.post('/api/chat/group/join', {group_id});
    }

    /**
     * 退出群聊
     * @param group_id
     */
    public exit(group_id: number) {
        return this.post('/api/chat/group/exit', {group_id});
    }
}

export default new Group();
