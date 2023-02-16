export const noEmptyElementsInArray = (array) => !array.includes('');

export function noEmptyLapSettings(array) {
    for (let i = 0; i < array.length; i++) {
        if ('' === array[i].breaths || '' === array[i].waitingTime) {
            return false
        }
    }
    return true
}

export * from '@vuelidate/validators'