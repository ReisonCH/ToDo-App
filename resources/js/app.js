import "./bootstrap";

import { createApp } from "vue/dist/vue.esm-bundler.js";
import router from "./router";
import store from "./store";
import axios from "axios";

axios.interceptors.request.use(
    (config) => {
        const token = store.state.token;
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

const app = createApp({});

app.use(router);
app.use(store);

app.mount("#app");
