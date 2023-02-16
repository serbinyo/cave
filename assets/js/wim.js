import '../css/wim.css';
import Vue from "vue";
import App from "./wim/App";
import VueAudio from 'vue-audio-better'

Vue.use(VueAudio);

new Vue({
    render: h => h(App)
}).$mount('#app');
