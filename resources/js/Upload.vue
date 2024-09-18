<template>
    <div class="form-floating ee-form-input">
        <div v-if="image" class="ee-form-uploader" @click="upload">
            <img :src="default" :id="id" class="ee-form-image">
            <div v-if="!selected" class="ee-form-upload-text">
                Browse for File Upload
                <ul v-if="!hideDetails && errorList.length > 0" class="form-errors ee-form-errors mb-2">
                    <li v-for="(error, i) in errorList" :key="id+'-error-'+i" class="form-error ee-form-error">{{ error }}</li>
                </ul>
            </div>
            <div v-else class="ee-form-upload-clear" @click="clear"><span class="material-icons">close</span></div>
        </div>
        <input v-show="false" type="file" :ref="'uploader-'+id" @change="change">
    </div>
</template>
<script>
export default {
    data: function() {
        return {
            id: 'vm-file_'+Math.random().toString(36).slice(2),
            default: this.$url+'default.png',
            selected: false,
            details: {},
            loading: false,
            errorList: [],
        }
    },
    methods: {
        upload: function() {
            if (!this.selected) {
                this.errorList = [];
                this.$refs['uploader-'+this.id].click();
            }
        },
        view: function(event) {
            if (!this.image) {
                return false;
            }
            this.errorList = [];
            this.loading = true;
            let input = event.target;
            var image = document.getElementById(this.id);
            var _this = this;
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    image.src = e.target.result;
                    let extension = input.files[0].name.split('.');
                    // Gets the details for the specific image to do appropraite comparisons
                    setTimeout(() => {
                        try {
                            _this.details = {
                                name: input.files[0].name,
                                type: input.files[0].type,
                                extension: extension[extension.length - 1],
                                width: image.naturalWidth,
                                height: image.naturalHeight,
                                ratio: _this.reduce((image.naturalWidth / image.naturalHeight * 10), 10),
                                size: input.files[0].size,
                            };
                            // Checks to make sure the type is correctly set
                            if (_this.accept.split(',').indexOf(_this.details.type) == -1) {
                                throw new Error(`The file\'s extension must be one of the following types: ${_this.accept}.`);
                            }
                            // Checks to make sure the image uploaded is a square
                            if (_this.square && image.naturalHeight != image.naturalWidth) {
                                throw new Error('The image selected must be a square.');
                            }
                            // Checks to make sure the ratio is correctly uploaded
                            if (_this.ratio != _this.details.ratio) {
                                throw new Error(`The image\'s ratio does not match ${_this.ratio}.`);
                            }
                        } catch ({ name, message }) {
                            _this.clear();
                            _this.errorList = [message];
                        }
                    }, 10);
                }
                reader.readAsDataURL(input.files[0]);
            }
            setTimeout(() => {
                this.loading = false;
            }, 1000);
        },
        change: function(event) {
            this.selected = true;
            this.view(event);
            this.$emit('update:modelValue', event.target.files[0])
        },
        clear: function() {
            this.errorList = [];
            this.value = '';
            let image = document.getElementById(this.id);
            image.src = this.default;
            this.details = {};
            setTimeout(() => {
                this.selected = false;
            }, 10);
        },
        currentImage: function() {
            return this.value != null && typeof(this.value) != 'object' ? (this.$url + this.value) : this.default;
        },
        reduce: function (numerator, denominator){
            // Finds the greatest common denominator
            var gcd = function gcd(numerator, denominator) {
                return denominator ? gcd(denominator, numerator % denominator) : numerator;
            };
            gcd = gcd(numerator, denominator);
            // Outputs the ratio / fraction for use an comparison
            return (numerator / gcd) + '/' + (denominator / gcd);
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
        modelValue: {
            immediate: true,
            handler: function(value) {
                let image = document.getElementById(this.id);
                if (image != null) {
                    image.src = this.default;
                }
            },
            deep: true,
        },
        errors: {
            immediate: true,
            handler: function(errors) {
                this.errorList = errors;
            },
            deep: true,
        },
    },
    props: {
        modelValue: { type: [File, String], default: '' },
        label: { type: String, default: null },
        accept: { type: String, default: 'image/png,image/jpeg,image/jpg' },
        image: { type: Boolean, default: true },
        square: { type: Boolean, default: true },
        ratio: { type: String, default: null },
        errors: { type: [Array, Object], default: [] },
        disabled: { type: Boolean, default: false },
        placeholder: { type: String, default: '' },
        hideDetails: { type: Boolean, default: false },
    }
}
</script>