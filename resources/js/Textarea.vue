<template>
    <div class="form-floating ee-form-input"
        :class="{ 'ee-no-label': label == null || label == '' }">
        <textarea
            v-model="value"
            :type="type"
            :id="id"
            class="form-control ee-form-control"
            :class="[{ 'is-invalid': errorList.length > 0, 'empty': value == '' || value == null }, inputClass]"
            :disabled="disabled"
            :readonly="readonly"
            :placeholder="placeholder"
            :maxlength="maxlength"
            :style="{ 'height': height }"
            @input="errorList = []"></textarea>
        <label v-if="label != null && label != ''" :for="id" class="form-label ee-form-label">{{ label }}</label>
        <ul v-if="!hideDetails" class="form-errors ee-form-errors mb-2">
            <li v-for="(error, i) in errorList" :key="id+'-error-'+i" class="form-error ee-form-error">{{ error }}</li>
        </ul>
    </div>
</template>
<script>
export default {
    data: function() {
        return {
            id: 'vm-textarea_'+Math.random().toString(16).slice(2),
            // Calculates the height based on the total rows to allow for floating labels with ease
            height: ((parseInt(this.rows) + 1) * 25) + 'px',
            errorList: [],
        }
    },
    computed: {
        value: {
            get: function () {
                return this.modelValue;
            },
            set: function (value) {
                this.$emit('update:modelValue', value);
            }
        }
    },
    watch: {
        errors: {
            immediate: true,
            handler: function(errors) {
                this.errorList = errors;
            },
        },
    },
    props: {
        modelValue: { type: [String, Number], default: '' },
        label: { type: String, default: null },
        placeholder: { type: String, default: '' },
        rows: { type: Number, default: 2 },
        maxlength: { type: Number, default: null },
        inputClass: { type: String, default: '' },
        errors: { type: [Array, Object], default: [] },
        disabled: { type: Boolean, default: false },
        readonly: { type: Boolean, default: false },
        hideDetails: { type: Boolean, default: false },
    }
}
</script>