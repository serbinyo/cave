<template>
    <div class="col-md-4 border-right">
        <div class="settings-tray">
            <h4>Беседы</h4>
            <!--
            todo блок с полезными ссылками
            <img class="profile-image" src="/assets/img/avatars/1.png"
                 alt="Profile img">
            <span class="settings-tray&#45;&#45;right">
                <i class="material-icons">cached</i>
                <i class="material-icons">message</i>
                <i class="material-icons">menu</i>
	    </span>-->
        </div>
        <!--
        todo поиск по беседам
        <div class="search-box">
            <div class="input-wrapper">
                <i class="material-icons">search</i>
                <input placeholder="Search here" type="text">
            </div>
        </div>-->

        <template v-for="(conversation, index, key) in CONVERSATIONS">
            <Conversation :conversation="conversation"/>
        </template>
    </div>

</template>

<script>
import {mapGetters} from 'vuex';
import Conversation from "./Conversation";

export default {
    components: {Conversation},
    computed  : {
        ...mapGetters(["CONVERSATIONS", "HUBURL", "USERNAME"])
    },
    methods   : {
        updateConversations(data) {
            this.$store.commit("UPDATE_CONVERSATIONS", data)
        }
    },
    mounted() {
        const vm = this;
        this.$store.dispatch("GET_CONVERSATIONS")
            .then(() => {
                let url = new URL(this.HUBURL);
                url.searchParams.append('topic', `/conversations/${this.USERNAME}`)
                const eventSource = new EventSource(url, {
                    withCredentials: true
                })

                eventSource.onmessage = function (event) {
                    vm.updateConversations(JSON.parse(event.data))
                }
            })
    }
}
</script>
