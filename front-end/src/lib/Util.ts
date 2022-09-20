import store from "@/store";

export const isMobile = () => {
    // @ts-ignore
    let width = store.state.Client.clientWidth;
    if (width == 0) width = document.documentElement.clientWidth;
    return width <= 768;
};
export const emojiNameToImage = (msg: any) => {
    // @ts-ignore
    let emojiMap = store.state.Chat.expressionMap;
    if (msg === null) msg = '';
    return msg.replace(/\[!(\w+)]/gi, (str: string, match: any) => {
        var file = match;
        return emojiMap[file] ? "<img emoji-name=\"".concat(match, "\" src=\"").concat(emojiMap[file], "\"  class=\"editor-emoji\" />") : "[!".concat(match, "]");
    });
};

export const debounce = (fn: Function, wait: number = 100): Function => {
    let timer: number | null = null;
    return function (...args: any[]): Promise<any> {
        if (timer !== null) {
            clearTimeout(timer);
        }
        // @ts-ignore
        const that = this;
        return new Promise((resolve) => {
            timer = setTimeout(() => resolve(fn.apply(that, args)), wait);
        });
    }
};

export const throttle = (fn: Function, wait = 200) => {
    let last = 0;
    let that = this;
    return async function (...args: any[]) {
        var now = new Date().getTime();
        if (now - last > wait) {
            last = new Date().getTime();
            return fn.apply(that, args);
        }
        return null;
    };
};

export const playMessageAudio = () => {
    // @todo Uncaught (in promise) DOMException: play() failed because the user didn't interact with the document first.
    let audio = new Audio(require('@/assets/new_message.mp3'));
    audio.play();
}
