<template>
    <div class="ee-form-multi-entries">
        <label v-if="label != null && label != ''" class="ee-form-label mb-3">{{ label }}</label>
        <div class="ee-form-multi-entry" v-for="(item, multiIndex) in value">
            <div :class="{ 'input-group': !breakpoint('sm') }">
                <vm-input
                    v-if="type == 'input'"
                    v-model="item.value"
                    type="input"
                    :label="inputLabel"
                    :placeholder="placeholder"
                    :input-class="inputClass"
                    :disabled="disabled"
                    :readonly="readonly"
                    hide-details></vm-input>
                <vm-select
                    v-if="type == 'select'"
                    v-model="item.value"
                    :items="items"
                    :label="inputLabel"
                    :input-class="inputClass"
                    :disabled="disabled"
                    :readonly="readonly"
                    :yes-no="yesNo"
                    :new-entry="newEntry"
                    :new-entry-text="newEntryText"
                    :new-entry-value="newEntryValue"
                    hide-details></vm-select>
                <vm-input
                    v-if="item.value == '0'"
                    v-model="item.new_entry"
                    type="input"
                    :label="newEntryText"
                    :placeholder="placeholder"
                    :input-class="inputClass"
                    :class="{ 'mt-4': breakpoint('sm') }"
                    :disabled="disabled"
                    :readonly="readonly"
                    hide-details />
                <div class="ee-button-container" :class="{ 'd-grid mt-4': breakpoint('sm') }">
                    <button
                        @click="remove(multiIndex)"
                        class="btn btn-danger"
                        :disabled="removeAll == false && value.length == 1">{{ removeLabel }}</button>
                </div>
            </div>
            <ul v-if="!hideDetails" class="form-errors ee-form-errors mb-2">
                <li v-for="(error, i) in getErrors(multiIndex)"
                    :key="id+'-error-'+i"
                    class="form-error ee-form-error">{{ error }}</li>
            </ul>
        </div>
        <button v-if="addButton == true" @click="add" class="btn btn-lg btn-primary">{{ addLabel }}</button>
    </div>
</template>
<script>
export default {
    data: function() {
        return {
            id: 'vm-multi_'+Math.random().toString(16).slice(2),
            errorList: [],
            counter: 0,
            template: {
                id: 'NEW-0',
                value: '',
                new_entry: '',
            },
        }
    },
    mounted: function() {
        // Adds one to begin with
        if (this.startWithOne && this.value.length == 0) {
            this.add();
        }
    },
    methods: {
        add: function() {
            this.counter++;
            var template = JSON.parse(JSON.stringify(this.template));
            template.id = 'NEW-'+this.counter;
            this.value.push(template);
        },
        remove: function(index) {
            if (this.removeAll == false && this.value.length == 1) {
                return false;
            }
            // Clears the errors
            if (typeof(this.errorList[index]) != 'undefined') {
                this.errorList[index] = [];
            }
            this.value.splice(index, 1);
        },
        getErrors: function(index, key = '') {
            return typeof(this.errorList[index]) !== 'undefined' ? this.errorList[index + (key == '' ? '' : ('.'+key))] : [];
        },
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
        modelValue: { type: Array, default: [] },
        type: { type: String, default: 'input' },
        label: { type: String, default: null },
        placeholder: { type: String, default: '' },
        items: { type: [Object, Array], default: [] },
        yesNo: { type: Boolean, default: false },
        newEntry: { type: Boolean, default: false },
        newEntryText: { type: String, default: 'New Entry' },
        newEntryValue: { type: [String, Number], default: '0' },
        inputLabel: { type: String, default: null },
        inputClass: { type: String, default: '' },
        errors: { type: [Array, Object], default: [] },
        disabled: { type: Boolean, default: false },
        readonly: { type: Boolean, default: false },
        hideDetails: { type: Boolean, default: false },
        removeAll: { type: Boolean, default: false },
        startWithOne: { type: Boolean, default: true },
        addLabel: { type: String, default: 'Add' },
        addButton: { type: Boolean, default: true },
        removeLabel: { type: String, default: 'Remove' },
    }
}
</script>