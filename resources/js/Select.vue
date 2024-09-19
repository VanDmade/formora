<template>
    <div class="form-floating ee-form-input"
        :class="{ 'ee-no-label': label == null || label == '' }">
        <select
            v-model="value"
            :id="id"
            class="form-control form-select ee-form-control ee-form-select"
            :class="[{ 'is-invalid': errorList.length > 0, 'empty': value == '' || value == null }, inputClass]"
            :disabled="disabled"
            :readonly="readonly"
            @input="errorList = []">
            <option v-if="placeholder != ''" value="" disabled hidden>{{ placeholder }}</option>
            <option v-if="newEntry" :value="newEntryValue">{{ newEntryText }}</option>
            <option v-for="(item, itemIndex) in getItems()"
                :value="yesNo ? item.value : item[inputValue]">{{ yesNo ? item.text : item[inputText] }}</option>
        </select>
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
            id: 'vm-select_'+Math.random().toString(16).slice(2),
            yesOrNo: [{ value: '1', text: 'Yes'}, { value: '0', text: 'No'}],
            errorList: [],
        }
    },
    methods: {
        getItems: function() {
            return this.yesNo ? this.yesOrNo : this.items;
        }
    },
    computed: {
        value: {
            get: function () {
                return this.modelValue;
            },
            set: function (value) {
                // Allows for ease of creation of method if a new entry is selected and it'll trigger functionality in parent component
                if (this.newEntry && value == this.newEntryValue) {
                    this.$emit('newEntrySelected');
                }
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
        items: { type: [Array, Object], default: [] },
        inputValue: { type: String, default: 'value' },
        inputText: { type: String, default: 'text' },
        label: { type: String, default: null },
        placeholder: { type: String, default: '' },
        inputClass: { type: String, default: '' },
        errors: { type: [Array, Object], default: [] },
        disabled: { type: Boolean, default: false },
        readonly: { type: Boolean, default: false },
        hideDetails: { type: Boolean, default: false },
        yesNo: { type: Boolean, default: false },
        newEntry: { type: Boolean, default: false },
        newEntryText: { type: String, default: 'New Entry' },
        newEntryValue: { type: [String, Number], default: '0' },
    }
}
</script>