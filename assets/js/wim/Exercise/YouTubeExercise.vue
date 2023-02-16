<template>
    <div class="card">
        <div class="row row-bordered g-0">
            <div class="col-md-6">
                <h5 class="card-header m-0 me-2 pb-3">Упражнение</h5>

                <div class="px-2 text-center">
                    <div class="canvas mb-2" v-if="isExercise">
                        <iframe width="560" height="315"
                                src="https://www.youtube-nocookie.com/embed/mD3QwerSmLs?autoplay=1"
                                title="YouTube video player" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write;
                                encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                        </iframe>
                    </div>
                    <div v-else>
                        <p>Удели 15 минут, что бы тебя никто не отвлекал и попробуй сейчас</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <img src="/assets/img/icons/unicons/chart.png" alt="Exercise Panel" class="rounded">
                        </div>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="cardOpt6" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6" style="">
                                <a class="dropdown-item active">YouTube Вариант</a>
                                <a class="dropdown-item"
                                   @click="$emit('switchType', 'custom')">Свой вариант</a>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <div>
                            <div v-if="isSettings">
                                <p>
                                    Стандартное упражнение с Ютуб
                                </p>
                                <div class="row">
                                    <button class="btn btn-primary" @click="start">Старт</button>
                                </div>
                            </div>
                            <div v-else-if="!isSettings && isSaveWindow">
                                <p>Если нужно уточните данные или добавьте круги,
                                    в соответствии с выполненным упражнением, после этого нажмите Сохранить</p>
                                <lap-settings
                                    :saveResult="saveResult">
                                </lap-settings>
                            </div>
                            <div v-else-if="!isSettings">
                                <p>
                                    Следуй командам Вима для выполнения дыхательного упражнения
                                </p>
                                <p class="mt-2">
                                    Когда вы закончите,
                                    <button
                                        @click="goSave"
                                        class="btn
                                    btn-outline-warning mt-2">
                                        Перейдите к сохранению результата
                                    </button>
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>

import LapSettings from "../LapSettings";

const axios = require('axios').default;

export default {
    components: {LapSettings},
    props: {
        saveResult: {
            type: Function,
            default: null
        },
    },
    data      : () => ({
        isSettings      : true,
        isExercise      : false,
        isSaveWindow    : false,
        display         : '',
        //Время задержки дыхания на вдохе

        //стандартные настройки
        user_authorized: false
    }),
    methods: {
        start() {
            this.isSettings = false;
            this.isExercise = true;
        },
        //region settings tabs,
        goSave() {
            this.isSettings = false;
            this.isSaveWindow = true;
        },
    }
}
</script>
