<template>
    <div id="inhale-exhale" class="pano">
            <div class="circle">
            <div v-if="counter" class="count" id="haleCounter">{{breathCounter}}</div></div>
    </div>
</template>

<script>

import Speech from "./Simple/Speech";
export default {
    components: {Speech},
    props: {
        counter: {
            type: Boolean,
            default: true
        }
    },
    data: () => ({
        breathCounter : 1,
        inhaleExhaleTime: 4500
    }),
    methods: {
        beginBreathing() {
            setInterval(() => {
                this.breathCounter++;
            }, this.inhaleExhaleTime);
        }
    },
    mounted() {
        this.beginBreathing();
    }
}
</script>
