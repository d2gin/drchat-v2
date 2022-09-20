import {Http} from "@/lib/Http";

export class Passport extends Http {
    login(username: string, password: string) {
        return this.post('/api/passport/login', {username, password});
    }

    register(username: string, password: string, nickname: string) {
        return this.post('/api/passport/register', {
            username, password, nickname
        });
    }
}

export default new Passport();
