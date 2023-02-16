<template>
    <!-- Typing area -->
    <div class="row">
        <div class="col-12">
            <div class="chat-box-tray">
                <!--
                todo приделать выбор смайликов
                <i class="material-icons">sentiment_very_satisfied</i>-->
                <input type="text" placeholder="Напишите сообщение" id="chat-input"
                       v-model="content"
                       @keydown.enter.prevent
                       @keyup.enter.prevent="sendMessage"
                       aria-describedby="button-addon2">
                <!--                <i class="material-icons">mic</i>-->
                <button id="button-addon2" type="submit" @click.prevent="sendMessage"
                        class="btn btn-link"> <i class="material-icons">send</i></button>

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: () => ({
            content: ''
        }),
        methods: {
            sendMessage() {
                if ('' !== this.content) {

                    //Блокируем инпут и кнопку
                    const input = document.querySelector("#chat-input");
                    input.disabled = true;
                    const button = document.querySelector("#button-addon2");
                    button.disabled = true;

                    this.$store.dispatch("POST_MESSAGE", {
                        conversationId: this.$route.params.id,
                        content: this.content
                    }).
                    then(() => {
                        //Разблокируем инпут и кнопку
                        this.content = '';
                        input.disabled = false;
                        button.disabled = false;
                        input.focus();
                    })
                }
            }
        }
    }
</script>
