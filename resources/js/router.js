//import vueRouter from 'vue-router';
import { createRouter, createWebHistory, createWebHashHistory } from 'vue-router'
//import Vue from 'vue';

//Vue.use(vueRouter);

import Index from "./views/Index.vue";
import BusinessProfile from "./views/BusinessProfile.vue";
import BusinessProfilePage from "./views/BusinessProfilePage.vue";
import Menu from "./views/Menu.vue";
import LoyaltyCard from "./views/LoyaltyCard.vue";
import BusinessProfilePhotos from "./views/BusinessProfilePhotos.vue";
import BusinessProfileQRCode from "./views/BusinessProfileQRCode.vue";
import DisplayQRCode from "./views/DisplayQRCode.vue";
import ScanQRCode from "./views/ScanQRCode.vue";
import AwardStamps from "./views/AwardStamps.vue";
import Dashboard from "./views/Dashboard.vue";
import ShoppingCart from "./views/ShoppingCart.vue";
import Orders from "./views/Orders.vue";

const routes = [
    {
        name: "index",
        path: "/",
        component: Index
    },
    {
        name: "business-profile",
        path: "/business-profile",
        component: BusinessProfile
    },
    {
        name: "business-profile-page",
        path: "/business-profile-page/:id",
        component: BusinessProfilePage
    },
    {
        name: "menu",
        path: "/menu",
        component: Menu
    },
    {
        name: "loyalty-card",
        path: "/loyalty-card",
        component: LoyaltyCard
    },
    {
        name: "business-profile-photos",
        path: "/business-profile-photos",
        component: BusinessProfilePhotos
    },
    {
        name: "business-profile-qr-code",
        path: "/business-profile-qr-code",
        component: BusinessProfileQRCode
    },
    {
        name: "display-qr-code",
        path: "/display-qr-code",
        component: DisplayQRCode
    },
    {
        name: "scan-qr-code",
        path: "/scan-qr-code",
        component: ScanQRCode
    },
    {
        name: "award-stamps",
        path: "/award-stamps/:user_id",
        component: AwardStamps
    },
    {
        name: "dashboard",
        path: "/dashboard",
        component: Dashboard
    },
    {
        name: "shopping-cart",
        path: "/shopping-cart",
        component: ShoppingCart
    },
    {
        name: "orders",
        path: "/orders",
        component: Orders
    },
];
//export default routes

/*export default new vueRouter({
    mode: "history",
    routes
});*/

const router = createRouter({
  // 4. Provide the history implementation to use. We are using the hash history for simplicity here.
  history: createWebHashHistory(),
  routes, // short for `routes: routes`
})
export default router