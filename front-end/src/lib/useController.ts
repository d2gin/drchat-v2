import Controller from "@/interface/Controller";

export default (controller: Controller) => {
    let props = Object.getOwnPropertyNames(controller);
    let emptyFunction = () => {
    };
    let options: any = {
        setup: controller.setup.bind(controller),
        beforeRouteEnter: ('beforeRouteEnter' in controller && controller.beforeRouteEnter) ? controller.beforeRouteEnter.bind(controller) : emptyFunction,
        beforeRouteUpdate: ('beforeRouteUpdate' in controller && controller.beforeRouteUpdate) ? controller.beforeRouteUpdate.bind(controller) : emptyFunction,
        beforeRouteLeave: ('beforeRouteLeave' in controller && controller.beforeRouteLeave) ? controller.beforeRouteLeave.bind(controller) : emptyFunction,
    };
    props.forEach((k: any) => options[k] = Reflect.get(controller, k));
    return options;
};
