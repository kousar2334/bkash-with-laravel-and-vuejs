import Vue from "vue";
import App from "./App.vue";
import router from "./router";

import { BootstrapVue, IconsPlugin } from "bootstrap-vue";

// Import Bootstrap and BootstrapVue CSS files (order is important)
import "bootstrap/dist/css/bootstrap.css";
import "bootstrap-vue/dist/bootstrap-vue.css";

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue);

import VueFlashMessage from "vue-flash-message";
require("vue-flash-message/dist/vue-flash-message.min.css");
Vue.use(VueFlashMessage, {
    messageOptions: {
        timeout: 4000,
        important: false,
        autoEmit: true,
        pauseOnInteract: true,
    },
});

new Vue({
    router,
    render: (h) => h(App),
}).$mount("#app");
