export class Timer {

    domElement;
    TIME_LIMIT;
    FULL_DASH_ARRAY;
    WARNING_THRESHOLD;
    ALERT_THRESHOLD;
    COLOR_CODES;
    timePassed;
    timeLeft;
    timerInterval;
    remainingPathColor;

    constructor(domElement, timeLimit) {
        this.domElement = domElement;
        this.TIME_LIMIT = timeLimit;
        this.FULL_DASH_ARRAY = 283;
        this.WARNING_THRESHOLD = this.TIME_LIMIT / 100 * 20;
        this.ALERT_THRESHOLD = this.TIME_LIMIT / 100 * 5;
        this.COLOR_CODES = {
            info   : {
                color: "green"
            },
            warning: {
                color    : "orange",
                threshold: this.WARNING_THRESHOLD
            },
            alert  : {
                color    : "red",
                threshold: this.ALERT_THRESHOLD
            }
        };

        this.timePassed = 0;
        this.timeLeft = this.TIME_LIMIT;
        this.timerInterval = null;
        this.remainingPathColor = this.COLOR_CODES.info.color;
    }

    startTimer() {
        if (this.TIME_LIMIT == 0) return;

        this.resetTimer();

        document.getElementById(this.domElement).innerHTML = `
                <div class="base-timer">
                  <svg class="base-timer__svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <g class="base-timer__circle">
                      <circle class="base-timer__path-elapsed" cx="50" cy="50" r="45"></circle>
                      <path
                        id="base-timer-path-remaining-${this.domElement}"
                        stroke-dasharray="283"
                        class="base-timer__path-remaining ${this.remainingPathColor}"
                        d="
                          M 50, 50
                          m -45, 0
                          a 45,45 0 1,0 90,0
                          a 45,45 0 1,0 -90,0
                        "
                      ></path>
                    </g>
                  </svg>
                  <span id="base-timer-label-${this.domElement}" class="base-timer__label">${this.formatTime(
            this.timeLeft
        )}</span>
                </div>
                `;

        this.runTimer();
    }

    onTimesUp() {
        clearInterval(this.timerInterval);
        document.getElementById(this.domElement).innerHTML = '';
    }

    resetTimer() {
        this.timePassed = 0;
        this.timeLeft = this.TIME_LIMIT;
        this.timerInterval = null;
        this.remainingPathColor = this.COLOR_CODES.info.color;
    }


    runTimer() {
        let that = this;

        this.timerInterval = setInterval(() => {
            that.timePassed = that.timePassed += 1;
            that.timeLeft = that.TIME_LIMIT - that.timePassed;
            document.getElementById(`base-timer-label-${that.domElement}`).innerHTML = that.formatTime(
                that.timeLeft
            );
            that.setCircleDasharray();
            that.setRemainingPathColor(that.timeLeft);

            if (that.timeLeft === 0) {
                that.onTimesUp();
            }
        }, 1000);

        return true
    }

    formatTime(time) {
        const minutes = Math.floor(time / 60);
        let seconds = time % 60;

        if (seconds < 10) {
            seconds = `0${seconds}`;
        }

        return `${minutes}:${seconds}`;
    }

    setRemainingPathColor(timeLeft) {
        const {alert, warning, info} = this.COLOR_CODES;
        if (timeLeft <= alert.threshold) {
            document
                .getElementById(`base-timer-path-remaining-${this.domElement}`)
                .classList.remove(warning.color);
            document
                .getElementById(`base-timer-path-remaining-${this.domElement}`)
                .classList.add(alert.color);
        } else if (timeLeft <= warning.threshold) {
            document
                .getElementById(`base-timer-path-remaining-${this.domElement}`)
                .classList.remove(info.color);
            document
                .getElementById(`base-timer-path-remaining-${this.domElement}`)
                .classList.add(warning.color);
        }
    }

    calculateTimeFraction() {
        const rawTimeFraction = this.timeLeft / this.TIME_LIMIT;
        return rawTimeFraction - (1 / this.TIME_LIMIT) * (1 - rawTimeFraction);
    }

    setCircleDasharray() {
        const circleDasharray = `${(
            this.calculateTimeFraction() * this.FULL_DASH_ARRAY
        ).toFixed(0)} 283`;
        document
            .getElementById(`base-timer-path-remaining-${this.domElement}`)
            .setAttribute(`stroke-dasharray`, circleDasharray);
    }
}