<template>
    <div class="col-md-8 chat-panel" ref="messagesBody">
        <!--
        todo подумать над шапкой окна с сообщениями
        <div class="settings-tray">
            <div class="friend-drawer no-gutters friend-drawer&#45;&#45;grey">
                <img class="profile-image" src="https://www.clarity-enhanced.net/wp-content/uploads/2020/06/robocop.jpg" alt="">
                <div class="text">
                    <h6>Robo Cop</h6>
                    <p class="text-muted">Layin' down the law since like before Christ...</p>
                </div>
                <span class="settings-tray&#45;&#45;right">
			  <i class="material-icons">cached</i>
			  <i class="material-icons">message</i>
			  <i class="material-icons">menu</i>
			</span>
            </div>
        </div>-->
        <div>
            <template v-for="(message, index, key) in MESSAGES">
                <Message :message="message"/>
            </template>
        </div>


        <Input v-if="MESSAGES" />
    </div>
</template>

<script>
    import {mapGetters} from 'vuex';
    import Message from "./Message";
    import Input from "./Input";

    export default {
        data: () => ({
            eventSource: null
        }),
        components: {Message, Input},
        computed: {
            ...mapGetters(["HUBURL", "USERNAME"]),
            MESSAGES() {
                return this.$store.getters.MESSAGES(this.$route.params.id);
            }
        },
        methods: {
            scrollDown() {
                this.$refs.messagesBody.scrollTop = this.$refs.messagesBody.scrollHeight;
                window.scrollTo(0, this.$refs.messagesBody.scrollHeight);
            },
            addMessage(data) {
                this.$store.commit("ADD_MESSAGE", {
                    conversationId: this.$route.params.id,
                    payload: data
                })
            }
        },
        mounted() {
            const vm = this;
            this.$store.dispatch("GET_MESSAGES", this.$route.params.id)
                .then(() => {
                    this.scrollDown();
                    if (null === this.eventSource) {
                        let url = new URL(this.HUBURL);
                        //url.searchParams.append('topic', `/conversations/${this.$route.params.id}`) Было у автора, но задваивались сообщения в правой стороне
                        url.searchParams.append('topic', `/conversations/${this.USERNAME}`);
                        this.eventSource = new EventSource(url, {
                            withCredentials: true
                        });

                        this.eventSource.onmessage = function (event) {
                            vm.addMessage(JSON.parse(event.data))
                        }
                    }

                })
        },
        watch: {
            MESSAGES: function (val) {
                this.$nextTick(() => {
                    this.scrollDown();
                })
            }
        },
        beforeDestroy() {
            if (this.eventSource instanceof EventSource) {
                this.eventSource.close();
            }
        }
    }
</script>
