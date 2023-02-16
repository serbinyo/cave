<template>
    <div id="wim-exercise">
        <div v-if="!isExerciseDone">
            <you-tube-exercise
                v-if="'youtube' === exerciseType"
                @switchType="switchExerciseType"
                :saveResult="addResult"></you-tube-exercise>
            <custom-exercise
                v-if="'custom' === exerciseType"
                @switchType="switchExerciseType"
                :saveResult="addResult"
            ></custom-exercise>
        </div>
        <div v-else>
            <div class="card">
                <div class="row row-bordered g-0">
                    <div class="col text-center mt-4">
                        <img src="/assets/img/elements/fi-success.png"
                             alt="You did it" class="rounded mb-5">
                        <p class="fw-semibold">Результат сохранен</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import YouTubeExercise from "./Exercise/YouTubeExercise";
import CustomExercise from "./Exercise/CustomExercise";

const axios = require('axios').default;

export default {
    components: {CustomExercise, YouTubeExercise},
    data      : () => ({
        isExerciseDone : false,
        user_authorized: false,
        exerciseType : 'youtube',
    }),
    mounted() {
        let that = this;

        axios.get(
            '/user/check'
        )
            .then(function (response) {
                // handle success
                if (200 === response.status) {
                    that.user_authorized = response.data.result;
                } else {
                    console.error()
                }
            })
    },
    methods: {
        switchExerciseType(type) {
            this.exerciseType = type;
        },
        addResult(laps) {
            let that = this;

            if (this.user_authorized) {
                let data = new FormData();
                data.append('laps', JSON.stringify(laps));
                axios.post(
                    '/breath/add',
                    data
                )
                    .then(function (response) {
                        // handle success
                        that.isExerciseDone = true;
                        console.log('Результат сохранен')
                    })
            } else {
                console.error('Зарегистрируйтесь, что бы ваш прогресс сохранялся');
            }
        },
    }
}
</script>
