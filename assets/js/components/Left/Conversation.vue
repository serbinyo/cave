<template>
    <router-link :to="{name: 'conversation', params: {id: conversation.conversationId}}" class="friend-drawer friend-drawer--onhover">
            <img class="profile-image"
                 src="https://res.cloudinary.com/mhmd/image/upload/v1564960395/avatar_usae7z.svg" alt="">
            <div class="text">
                <h6>{{ conversation.name }}</h6>
                <p class="text-muted">{{ conversation.content }}</p>
            </div>
            <span class="time text-muted small">{{ date }}</span>
    </router-link>
</template>

<script>
    export default {
        props: {
            conversation: Object
        },
        computed: {
            date() {
                return new Date(this.conversation.createdAt).toLocaleTimeString();
            }
        }
    }
</script>
