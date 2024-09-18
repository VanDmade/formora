<template>
    <div class="ee-form-multi-entries">
        <label v-if="label != null && label != ''" class="ee-form-label mb-3">{{ label }}</label>
        <div class="ee-form-multi-entry" v-for="(item, multiIndex) in value">
            <div class="input-group">
                <vm-input
                    v-model="item.value"
                    type="input"
                    :label="inputLabel"
                    :placeholder="placeholder"
                    :inputClass="inputClass"
                    :errors="getErrors(multiIndex)"
                    :disabled="disabled"
                    :readonly="readonly"
                    hide-details></vm-input>
                <button
                    @click="remove(multiIndex)"
                    class="btn btn-danger"
                    :disabled="removeAll == false && value.length == 1">{{ removeLabel }}</button>
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
        getErrors: function(index) {
            return typeof(this.errorList[index]) !== 'undefined' ? this.errorList[index] : [];
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
        modelValue: { type: [Array], default: '' },
        label: { type: String, default: null },
        placeholder: { type: String, default: '' },
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