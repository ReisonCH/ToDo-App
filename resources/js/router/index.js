import { createRouter, createWebHistory } from "vue-router";
import Login from "../components/Login.vue";
import Register from "../components/Register.vue";
import NotesList from "../components/NotesList.vue";
import NoteForm from "../components/NoteForm.vue";
import store from "../store";

const routes = [
    {
        path: "/login",
        component: Login,
    },
    {
        path: "/register",
        component: Register,
    },
    {
        path: "/notes",
        component: NotesList,
    },
    {
        path: "/notes/new",
        component: NoteForm,
    },
    {
        path: "/notes/edit/:id",
        component: NoteForm,
        props: true,
    },
    {
        path: "/:pathMatch(.*)*",
        redirect: "/login",
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const token = store.state.token;

    if ((to.path === "/login" || to.path === "/register") && token) {
        next("/notes");
    } else {
        next();
    }
});

export default router;
