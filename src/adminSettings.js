import Vue from 'vue';
import AdminSettings from "./components/AdminSettings";

document.addEventListener('DOMContentLoaded', main);

function main () {
    Vue.prototype.t = t;
    Vue.prototype.n = n;
    Vue.prototype.OC = window.OC;
    Vue.prototype.OCA = window.OCA;

    const View = Vue.extend(AdminSettings);
    const view = new View();
    view.$mount('#riot-chat-settings');
}
