import Controller from "@/interface/Controller";
import Cameras from "@/views/Message/Video/Cameras.vue";
import {computed, onMounted, reactive, Ref, ref, unref, watch} from "vue";
import 'webrtc-adapter';
import {UnwrapNestedRefs} from "@vue/reactivity";
import Config from "@/lib/Config";
import {Socketio, useSocketio} from "@/lib/Socket/Socketio";
import {message, Modal} from "ant-design-vue";
import Friend from "@/api/Friend";
import {
    NavigationGuardNext,
    onBeforeRouteLeave,
    RouteLocationNormalized,
    RouteLocationNormalizedLoaded,
    Router,
    useRoute,
    useRouter
} from "vue-router";
import {Store, useStore} from "vuex";

interface CameraInfo {
    readonly deviceId: string;
    readonly groupId: string;
    readonly kind: MediaDeviceKind;
    readonly label: string;
    readonly type: "front" | "back";
}

interface FaceMe {
    video: boolean | MediaTrackConstraints,
    audio: boolean | MediaTrackConstraints,
    booted: boolean,
    muted: boolean,
    sound_off: boolean,
    camera_off: boolean,
    camera: CameraInfo,
    stream?: MediaStream,
    fullscreen: boolean
}

interface FaceHim {
    booted: boolean,
    camera_type: 'front' | 'back',
    fullscreen: boolean,
}

interface Devices {
    camera: {
        front: CameraInfo[],
        back: CameraInfo[],
        all: CameraInfo[],
    },
    all: MediaDeviceInfo[],
}

export default class VideoChatController implements Controller {
    protected components = {Cameras,};
    protected devices!: Devices;
    protected faceme!: UnwrapNestedRefs<FaceMe>;
    protected facehim!: UnwrapNestedRefs<FaceHim>;
    protected cameraDrag!: UnwrapNestedRefs<any>;
    protected peerConnection!: UnwrapNestedRefs<RTCPeerConnection>;
    protected facemeRef: Ref = ref();
    protected facehimRef: Ref = ref();
    protected socketio!: Socketio;
    protected friend: Ref = ref();
    protected route!: RouteLocationNormalizedLoaded;
    protected router!: Router;
    protected store!: Store<any>;
    protected facingMode = {front: 'user', back: 'environment'};
    protected inviteAsk!: UnwrapNestedRefs<any>;
    protected cameraPick!: Ref<boolean>;

    setup(props: any, context: any): any {
        this.socketio = useSocketio();
        this.route = useRoute();
        this.router = useRouter();
        this.store = useStore();
        this.cameraPick = ref(false);

        this.devices = reactive({
            camera: {
                front: [],
                back: [],
                all: []
            },
            all: [],
        });
        this.facehim = reactive({
            booted: false,
            camera_type: 'front',
            fullscreen: false,
        });
        this.faceme = reactive({
            video: true,
            audio: true,
            muted: false,
            booted: false,
            fullscreen: true,
            sound_off: false,
            camera_off: false,
            camera: <any>{},
        });
        this.cameraDrag = reactive({
            start: false,
            xDiffer: 0,
            yDiffer: 0,
        });
        this.inviteAsk = reactive({
            result: '',
            resultMap: {
                'accept': '????????????...',
                'connected': '?????????????????????????????????...',
                'no-media': '?????????????????????????????????',
                'reject': '???????????????????????????????????????',
            }
        });

        onMounted(async () => {
            // ???????????????
            this.initSocketEvents();
            this.initDevices()
                .then(devices => this.bootCamera('front'))
                .then(stream => {
                    if (<any>this.route.query.accept != 1) {
                        console.log('opened');
                        this.publish({
                            friend_id: this.friend.value.friend_id,
                            event: 'call-up',
                            data: {
                                sender: this.store.state.User.info.id,
                                type: 'video'
                            },
                        });
                    } else if (<any>this.route.query.accept == 1 && !this.peerConnection) {
                        console.log('connected by invite');
                        this.publish({
                            friend_id: this.friend.value.friend_id,
                            event: 'call-invite-feedback',
                            data: {
                                sender: this.store.state.User.info.id,
                                result: 'connected'
                            },
                        });
                    }
                });
        });
        // ??????????????????
        onBeforeRouteLeave((to, from, next) => {
            this.handUp(false);
            this.socketio.off('video-event');
            next();
        });
        return {
            faceme: this.faceme,
            facehim: this.facehim,
            facemeRef: this.facemeRef,
            facehimRef: this.facehimRef,
            friend: this.friend,
            inviteAsk: this.inviteAsk,
            devices: this.devices,
            cameras: this.devices.camera.all,
            cameraPick: this.cameraPick,
            isPeerConnection: computed(() => !!this.peerConnection),
            facemeClasses: computed(() => ({
                //'front': this.faceme.camera.type == 'front',
                'hide': !this.faceme.booted,
                'full-screen': this.faceme.fullscreen,
                'float-screen': !this.faceme.fullscreen,
                'trans-none': this.cameraDrag.start,
            })),
            facehimClasses: computed(() => ({
                //'front': this.facehim.camera_type == 'front',
                'hide': !this.facehim.booted,
                'full-screen': !this.faceme.fullscreen,
                'float-screen': this.faceme.fullscreen,
                'trans-none': this.cameraDrag.start,
            })),

            handleFullscreen: this.handleFullscreen.bind(this),
            handleMouse: this.handleMouse.bind(this),
            handleTouch: this.handleTouch.bind(this),
            handleToggleCamera: this.handleToggleCamera.bind(this),
            handleToggleMuted: this.handleToggleMuted.bind(this),
            handleToggleSound: this.handleToggleSound.bind(this),
            handleToggleSwitchCamera: this.handleToggleSwitchCamera.bind(this),
            handlePickCamera: this.handlePickCamera.bind(this),
        };
    }

    public beforeRouteEnter(to: RouteLocationNormalized, from: RouteLocationNormalized, next: NavigationGuardNext) {
        let friend_id = <any>to.query.friend_id;
        if (!friend_id) {
            message.error('????????????');
            this.router.go(-1);
            return false;
        }
        Friend.loading(true).info(friend_id).then(res => {
            this.friend.value = reactive(res.data);
            next();
        }, e => {
            message.error(e.message);
            this.router.go(-1);
        });
    }

    /**
     * ????????????
     */
    protected initSocketEvents() {
        this.socketio.on('video-event', (response: any) => {
            switch (response.event) {
                case 'client-answer':
                    console.log('client-answer', response);
                    if (this.peerConnection) {
                        this.peerConnection.setRemoteDescription(new RTCSessionDescription(response.data));
                    }
                    break;
                case 'client-candidate':
                    console.log('client-candidate', response);
                    if (response.data && this.peerConnection) {
                        this.peerConnection.addIceCandidate(new RTCIceCandidate(response.data));
                    }
                    break;
                case 'client-offer':
                    console.log('client-offer');
                    if (!this.peerConnection) {
                        this.createPeerConnection();
                        this.addTracks();
                    }
                    // ??????????????????ontrack??????
                    // ????????????????????????onicecandidate??????
                    this.peerConnection.setRemoteDescription(new RTCSessionDescription(response.data))
                        .then(() => this.peerConnection.createAnswer())
                        .then(description => this.peerConnection.setLocalDescription(description))
                        .then(() => {
                            this.publish({
                                event: 'client-answer',
                                friend_id: this.friend.value.friend_id,
                                data: this.peerConnection.localDescription
                            });
                        });
                    break;
                case 'call-invite-feedback':
                    console.log('call-invite-feedback');
                    this.inviteAsk.result = response.data.result;
                    if (response.data.result == 'reject') {
                        Modal.error({title: '???????????????????????????????????????'});
                        this.router.go(-1);
                    } else if (response.data.result == 'accept') {
                        message.success('???????????????????????????');
                    } else if (response.data.result == 'no-media') {
                        Modal.error({title: '?????????????????????????????????'});
                        this.router.go(-1);
                    } else if (response.data.result == 'outline') {
                        Modal.error({
                            title: '???????????????',
                            okText: '??????',
                            onOk: () => this.router.go(-1),
                        });
                    } else if (response.data.result == 'hand-up') {
                        Modal.info({
                            title: '???????????????',
                            okText: '??????',
                            onOk: () => this.router.go(-1),
                        });
                    } else if (response.data.result == 'connected') {
                        message.success('?????????????????????????????????');
                        this.createPeerConnection();
                        // ????????????track ??????localDescription??????????????????
                        if (this.faceme.stream) {
                            this.faceme.stream.getTracks().forEach(track => {
                                console.log('addtrack');
                                this.peerConnection.addTrack(track, <MediaStream>this.faceme.stream);
                            });
                        } else {
                            message.success('????????????');
                        }
                    }
                    break;
                case 'call-up':
                    if (this.route.query.accept) {
                        let next: Promise<any> = Promise.resolve();
                        if (!this.peerConnection.localDescription) {
                            next = this.createOffer();
                        }
                        next.then(() => {
                            this.publish({
                                friend_id: this.friend.value.friend_id,
                                event: 'client-offer',
                                data: this.peerConnection.localDescription,
                            });
                        });
                    }
                    break;
            }
        });
    }

    /**
     * ?????????????????????
     */
    protected initDevices() {
        return navigator.mediaDevices.enumerateDevices().then((devices: MediaDeviceInfo[]) => {
            this.devices.camera.front = [];
            this.devices.camera.back = [];
            this.devices.camera.all = [];
            this.devices.all = [...devices];
            devices.forEach(device => {
                if (device.kind.search(/video/i) < 0) {
                    return;
                }
                let group = device.label.match(/camera\d*\s+(\d+)/i);
                let back_id: number = (group ? group[1] : this.devices.camera.back.length) as number;
                let front_id: number = (group ? group[1] : this.devices.camera.front.length) as number;
                let camera: CameraInfo = {
                    deviceId: device.deviceId,
                    groupId: device.groupId,
                    kind: device.kind,
                    label: device.label,
                    type: "front",
                };
                if (device.label.search(/fac[ing]*\s+back/i) >= 0) {
                    this.devices.camera.back[back_id] = {...camera, type: "back"};
                } else if (device.label.search(/fac[ing]*\s+front/i) >= 0) {
                    this.devices.camera.front[front_id] = {...camera, type: "front"};
                }
                this.devices.camera.all.push(camera);
            });
            this.devices.camera.front = Object.values(this.devices.camera.front);
            this.devices.camera.back = Object.values(this.devices.camera.back);
            return Promise.resolve(this.devices);
        });
    }

    // ??????
    protected handleFullscreen(type = 'faceme') {
        // @ts-ignore
        this[`${type}Ref`].value.style = {};
        this.faceme.fullscreen = type == 'faceme';
    }

    // ??????
    protected handleMouse(e: any) {
        if (e.type == 'mousedown') {
            if (!e.target.classList.contains('float-screen')) return;
            this.cameraDrag.start = true;
            this.cameraDrag.xDiffer = e.clientX - e.target.offsetLeft;
            this.cameraDrag.yDiffer = e.clientY - e.target.offsetTop;
            this.cameraDrag.element = e.target;
            document.onmouseup = () => {
                this.cameraDrag.start = false;
                this.cameraDrag.xDiffer = 0;
                this.cameraDrag.yDiffer = 0;
                document.onmouseup = null;
                document.onmousemove = null;
            };
            document.onmousemove = (e) => {
                if (!this.cameraDrag.start) return;
                e.preventDefault();
                var x = e.clientX - this.cameraDrag.xDiffer;
                var y = e.clientY - this.cameraDrag.yDiffer;
                this.cameraDrag.element.style.right = 0;
                this.cameraDrag.element.style.left = x + 'px';
                this.cameraDrag.element.style.top = y + 'px';
            };
        }
    }

    // ??????
    protected handleTouch(e: any) {
        if (e.type == 'touchstart') {
            if (!e.target.classList.contains('float-screen')) return;
            let touch = e.targetTouches[0];
            this.cameraDrag.start = true;
            this.cameraDrag.xDiffer = touch.clientX - e.target.offsetLeft;
            this.cameraDrag.yDiffer = touch.clientY - e.target.offsetTop;
        } else if (e.type == 'touchend') {
            this.cameraDrag.start = false;
            this.cameraDrag.xDiffer = 0;
            this.cameraDrag.yDiffer = 0;
        } else if (e.type == 'touchmove' && this.cameraDrag.start) {
            let touch = e.targetTouches[0];
            var x = touch.clientX - this.cameraDrag.xDiffer;
            var y = touch.clientY - this.cameraDrag.yDiffer;
            e.target.style.right = 0;
            e.target.style.left = x + 'px';
            e.target.style.top = y + 'px';
        }
    }

    /**
     * ???????????????
     */
    protected handleToggleCamera() {
        if (this.faceme.stream && ('getVideoTracks' in this.faceme.stream)) {
            this.faceme.stream.getVideoTracks().forEach(v => {
                this.faceme.camera_off = v.enabled;
                v.enabled = !v.enabled;
            });
        }
    }

    /**
     * ???????????????
     */
    protected handleToggleMuted() {
        if (!this.faceme.stream || !('getAudioTracks' in this.faceme.stream)) return;
        var tracks = this.faceme.stream.getAudioTracks();
        if (tracks.length == 0) {
            message.info("?????????????????????");
        }
        tracks.forEach(v => {
            this.faceme.muted = v.enabled;
            v.enabled = !v.enabled;
        })
    }

    /**
     * ????????????
     */
    protected handleToggleSound() {
        var volume = this.facehimRef.value.volume;
        this.facehimRef.value.volume = volume > 0 ? 0 : 1;
        this.faceme.sound_off = !this.facehimRef.value.volume;
    }

    /**
     * ???????????????
     */
    protected handleToggleSwitchCamera() {
        if (!this.faceme.stream || !this.faceme.camera) {
            return;
        } else if (
            (this.faceme.camera.type == 'front' && this.devices.camera.back.length < 1)
            || (this.faceme.camera.type == 'back' && this.devices.camera.front.length < 1)
        ) {
            // ???????????????????????????????????????????????????????????????
            this.cameraPick.value = true;
            return;
        }
        this.switchCamera(this.faceme.camera.type == 'front' ? 'back' : 'front');
    }

    /**
     * ??????????????????
     */
    protected createPeerConnection() {
        this.peerConnection = reactive(new RTCPeerConnection({iceServers: Config.iceServers}));
        // ??????setRemoteDescription?????????????????????
        this.peerConnection.onicecandidate = this.handleIceCandidateEvent.bind(this);
        // ??????addTrack?????????????????????
        this.peerConnection.onnegotiationneeded = this.handleNegotiationneededEvent.bind(this);
        // ???????????????????????????????????????
        this.peerConnection.ontrack = this.handleTrackEvent.bind(this);
    }

    /**
     *
     * @param event
     */
    protected handleIceCandidateEvent(event: any) {
        console.log('onicecandidate')
        if (event.candidate) {
            this.publish({
                event: 'client-candidate',
                friend_id: unref(this.friend).friend_id,
                data: event.candidate,
            });
        }
    }

    protected handleNegotiationneededEvent() {
        console.log('onnegotiationneeded')
        if (this.peerConnection.signalingState != "stable") {
            return;
        } else if (!this.peerConnection.localDescription) {
            this.createOffer().then(() => {
                this.publish({
                    event: 'client-offer',
                    friend_id: unref(this.friend).friend_id,
                    data: this.peerConnection.localDescription,
                });
            });
        }
    }

    /**
     * ???????????????????????????????????????????????????????????????
     * @param e
     */
    protected handleTrackEvent(e: any) {
        console.log('ontrack', e.streams[0])
        this.facehimRef.value.srcObject = e.streams[0];
        this.facehimRef.value.onloadedmetadata = () => {
            console.log('????????????????????????');
            this.facehim.booted = true;
            this.faceme.fullscreen = false;
            message.success('????????????');
        }
    }

    /**
     * ?????????????????????
     * @param index
     * @param camera
     */
    protected handlePickCamera(index: number, camera: CameraInfo) {
        this.faceme.video = {deviceId: camera.deviceId};
        this.switchCamera(this.faceme.camera.type);
    }

    /**
     * ????????????????????????
     */
    public createOffer() {
        if (!this.peerConnection)
            this.createPeerConnection();
        return this.peerConnection.createOffer({
            offerToReceiveAudio: true,
            offerToReceiveVideo: true,
        }).then(description => this.peerConnection.setLocalDescription(description));
    }

    /**
     * ??????????????????
     */
    protected addTracks() {
        // ????????????track ??????localDescription??????????????????
        this.faceme.stream?.getTracks().forEach(track => {
            this.peerConnection.addTrack(track, <MediaStream>this.faceme.stream);
        });
    }

    /**
     * ??????????????????
     * @param stream
     */
    protected replaceTrack(stream: MediaStream) {
        if (this.peerConnection && 'getTracks' in stream) {
            stream.getTracks().forEach(track => {
                let sender: RTCRtpSender | undefined = this.peerConnection.getSenders().find((s) => s.track?.kind == track.kind);
                if (track.kind == 'video') track.enabled = !this.faceme.camera_off;
                if (track.kind == 'audio') track.enabled = !this.faceme.muted;
                console.log('found sender:', sender);
                if (sender) sender.replaceTrack(track);
            });
        }
    }

    /**
     * ??????????????????
     * @param option
     */
    protected bootMedia(option: MediaStreamConstraints) {
        return navigator.mediaDevices.getUserMedia(option);
    }

    /**
     * ???????????????
     * @param type
     */
    protected bootCamera(type: 'front' | 'back') {
        let video_constraints = this.faceme.video;
        if (this.faceme.video && typeof this.faceme.video == 'boolean') {
            video_constraints = {facingMode: this.facingMode[type]};
            let device = this.getCameraDevice(type);
            if (device?.deviceId) {
                video_constraints = {deviceId: device.deviceId};
            }
        }
        let option = {
            video: video_constraints,
            audio: this.faceme.audio,
        };
        return this.bootMedia(option).then(stream => {
            let videoTracks = stream.getVideoTracks();
            console.log('Using video device: ' + videoTracks[0].label);
            this.faceme.booted = true;
            this.faceme.stream = stream;
            this.facemeRef.value.srcObject = stream;
            if (this.devices.all.length < 1) this.initDevices();
            return Promise.resolve(stream);
        }, e => {
            console.log(e);
            return Promise.reject(e);
        });
    }

    /**
     * ???????????????
     * @param type
     */
    protected switchCamera(type: 'front' | 'back') {
        if (this.faceme.stream && 'getTracks' in this.faceme.stream) {
            this.faceme.stream.getTracks().forEach(async strack => {
                strack.stop();
            });
            this.bootCamera(type).then((stream) => {
                this.replaceTrack(stream);
                this.publish({
                    event: 'client-switch-camera',
                    friend_id: this.friend.value.friend_id,
                    data: {
                        camera_type: this.faceme.camera.type,
                    },
                });
            });
        } else {
            this.bootCamera(type);
        }
    }

    /**
     * ?????????????????????
     * @param type
     */
    protected getCameraDevice(type: 'front' | 'back') {
        if (type == 'back' && this.devices.camera.back.length > 0) {
            this.faceme.camera = this.devices.camera.back[0];
        } else if (type == 'front' && this.devices.camera.front.length > 0) {
            this.faceme.camera = this.devices.camera.front[0];
        } else if (Object.keys(this.faceme.camera).length <= 0) {
            this.faceme.camera = this.devices.camera.all[0] || {};
        }
        return this.faceme.camera;
    }

    /**
     * ??????
     * @param navigate
     */
    protected handUp(navigate = false) {
        if (this.peerConnection) {
            console.log('hand-up');
            this.publish({
                event: 'call-invite-feedback',
                friend_id: this.friend.value.friend_id,
                data: {
                    result: 'hand-up',
                },
            });
        }
        this.destroyStream();
        this.closeRtc();
        if (navigate) this.router.go(-1);
    }

    /**
     * ??????/???????????????
     */
    destroyStream() {
        var stream = this.faceme.stream;
        if (stream && typeof stream.getTracks == 'function') {
            stream.getVideoTracks().forEach(v => {
                v.stop();
                console.log('??????', v.label);
                this.faceme.booted = false;
            });
            stream.getAudioTracks().forEach(v => {
                v.stop();
                console.log('??????', v.label);
                this.faceme.audio = false;
            });
            this.faceme.stream = undefined;
        }
    }

    /**
     * ??????????????????
     */
    protected closeRtc() {
        if (!this.peerConnection) return 0;
        this.peerConnection.onicecandidate = null;
        this.peerConnection.onnegotiationneeded = null;
        this.peerConnection.ontrack = null;
        this.peerConnection.close();
        this.peerConnection = <any>null;
    }

    /**
     * ??????
     * @param data
     */
    protected publish(data: { friend_id: number, event: string, data?: object | null }) {
        this.socketio.emit('video-event-publish', data);
    }
}
