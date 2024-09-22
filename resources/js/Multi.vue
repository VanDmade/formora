<template>
    <div class="ee-form-multi-entries">
        <label v-if="label != null && label != ''" class="ee-form-label mb-3">{{ label }}</label>
        <div class="ee-form-multi-entry" v-for="(item, multiIndex) in value">
            <div :class="{ 'input-group': !breakpoint('sm') }">
                <slot :value="value" :index="multiIndex" name="entry"></slot>
                <button
                    v-if="value.length > 1 && reorganize"
                    type="button"
                    :disabled="multiIndex == 0"
                    class="btn ee-btn-reorder btn-secondary px-1"
                    @click="reorderUp(multiIndex)"><span class="material-icons">arrow_upward</span></button>
                <button
                    v-if="value.length > 1 && reorganize"
                    type="button"
                    :disabled="multiIndex == value.length - 1"
                    class="btn ee-btn-reorder btn-secondary px-1"
                    @click="reorderDown(multiIndex)"><span class="material-icons">arrow_downward</span></button>
                <div class="ee-button-container" :class="{ 'd-grid mt-4': breakpoint('sm') }">
                    <button
                        type="button"
                        class="btn btn-danger"
                        :disabled="removeAll == false && value.length == 1"
                        @click="remove(multiIndex)">{{ removeLabel }}</button>
                </div>
            </div>
            <p v-if="slugs && ((item.value != '' && item.value != '0') || (newEntry && item.value == '0' && item.new_entry != ''))" class="notes mb-0 pl-1">{{ item.slug }}</p>
            <ul v-if="!hideDetails" class="form-errors ee-form-errors mb-2">
                <li v-for="(error, i) in getErrors(multiIndex)"
                    :key="id+'-error-'+i"
                    class="form-error ee-form-error">{{ error }}</li>
            </ul>
        </div>
        <button v-if="addButton == true" type="button" @click="add" class="btn btn-primary">{{ addLabel }}</button>
    </div>
</template>
<script>
export default {
    data: function() {
        return {
            id: 'vm-multi_'+Math.random().toString(16).slice(2),
            errorList: [],
            counter: 0,
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
            template.slug = 'slug-'+Math.random().toString(16).slice(10);
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
        reorderUp: function(index) {
            if (index == 0) {
                return;
            }
            let current = this.value[index];
            let before = this.value[index-1];
            this.value[index] = before;
            this.value[index-1] = current;
        },
        reorderDown: function(index) {
            if (index == this.value.length - 1) {
                return;
            }
            let current = this.value[index];
            let before = this.value[index+1];
            this.value[index] = before;
            this.value[index+1] = current;
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
        errors: { type: [Array, Object], default: [] },
        disabled: { type: Boolean, default: false },
        hideDetails: { type: Boolean, default: false },
        removeAll: { type: Boolean, default: false },
        startWithOne: { type: Boolean, default: true },
        addLabel: { type: String, default: 'Add' },
        addButton: { type: Boolean, default: true },
        removeLabel: { type: String, default: 'Remove' },
        slugs: { type: Boolean, default: false },
        reorganize: { type: Boolean, default: true },
        template: { type: Object, default: { id: '', value: '', slug: '' } },
    }
}
</script>
<style>

</style>