import {NavigationGuardNext, RouteLocationNormalized} from "vue-router";

export default interface Controller {
    setup: (props?: any, context?: any) => any,
    beforeRouteEnter?: (to:RouteLocationNormalized, from:RouteLocationNormalized, next:NavigationGuardNext) => any,
    beforeRouteUpdate?: (to:RouteLocationNormalized, from:RouteLocationNormalized, next:NavigationGuardNext) => any,
    beforeRouteLeave?: (to:RouteLocationNormalized, from:RouteLocationNormalized, next:NavigationGuardNext) => any,
}
