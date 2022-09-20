import {Http} from "@/lib/Http";

export class User extends Http {
    info() {
        return this.get('/api/user/info');
    }
}

export default new User();
