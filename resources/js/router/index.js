import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

const router = new VueRouter({
    mode: "history",
    routes: [
        {
            path: "/",
            name: "bkash",
            component: () =>
                import(/* webpackChunkName: "bkash" */ "../page/bkash.vue"),
        },
    ],
});

export default router;
