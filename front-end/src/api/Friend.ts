import {Http} from "@/lib/Http";

export class Friend extends Http {

    STATUS_NEW_REQUEST = 1;// 新验证
    STATUS_UNTIPS_REQUEST = 2;// 不提示
    STATUS_PASS_REQUEST = 3;// 通过
    STATUS_REFUSE_REQUEST = 4;// 不通过
    /**
     * 好友列表
     */
    list() {
        return this.get('/api/friend/list');
    }

    /**
     * 好友资料
     * @param friend_id
     */
    info(friend_id: number) {
        return this.get(`/api/friend/info?friend_id=${friend_id}`);
    }

    delete(friend_id: number) {
        return this.post('/api/friend/delete', {friend_id});
    }

    preciseSearch(keywords: string) {
        return this.get(`/api/friend/preciseSearch?keyword=${keywords}`);
    }

    /**
     * 好友请求
     * @param friend_id
     * @param remark_name
     * @param message
     */
    addRequest(friend_id: number, remark_name = '', message = '') {
        return this.post('/api/friend/request', {friend_id, remark_name, message});
    }

    /**
     * 通过请求
     * @param request_id
     */
    passRequest(request_id: number) {
        return this.post('/api/friend/request/pass', {request_id});
    }

    /**
     * 拒绝请求
     * @param request_id
     */
    refuseRequest(request_id: number) {
        return this.post('/api/friend/request/refuse', {request_id});
    }

    /**
     * 请求列表
     */
    requestList() {
        return this.get('/api/friend/request/list');
    }

    hintCount() {
        return this.get('/api/friend/hintCount');
    }
}

export default new Friend();
