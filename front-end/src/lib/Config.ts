export default {
    api: process.env.VUE_APP_API_URL,
    websocket: process.env.VUE_APP_WEBSOCKET,
    iceServers: [
        {
            urls: process.env.VUE_APP_ICE_URLS,
            username: process.env.VUE_APP_ICE_USERNAME,
            credential: process.env.VUE_APP_ICE_CREDENTIAL,
        },
    ],
}
