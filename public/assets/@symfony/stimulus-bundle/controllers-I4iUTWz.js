import controller_0 from "../ux-live-component/live_controller.js";
import "../ux-live-component/live.min.css";
import controller_1 from "../ux-turbo/turbo_controller.js";
import controller_2 from "../../controllers/hello_controller.js";
export const eagerControllers = {"live": controller_0, "symfony--ux-turbo--turbo-core": controller_1, "hello": controller_2};
export const lazyControllers = {"csrf-protection": () => import("../../controllers/csrf_protection_controller.js")};
export const isApplicationDebug = true;