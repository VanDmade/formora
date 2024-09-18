<template>
    <div class="form-floating ee-form-input">
        <div v-if="image" class="ee-form-uploader" @click="upload">
            <img :src="default" :id="id" class="ee-form-image">
            <div v-if="!selected" class="ee-form-upload-text">Browse for File Upload</div>
            <div v-else class="ee-form-upload-clear" @click="clear"><span class="material-icons">close</span></div>
        </div>
        <input v-show="false" type="file" :ref="'uploader-'+id" @change="change">
    </div>
    {{ details }}
</template>
<script>
export default {
    data: function() {
        return {
            id: 'vm-file_'+Math.random().toString(36).slice(2),
            default: this.$url+'default.png',
            selected: false,
            details: {
                name: '',
                type: '',
                extension: '',
                width: 0,
                height: 0,
                ratio: 0,
                size: 0,
            },
            loading: false,
            errorList: [],
        }
    },
    methods: {
        upload: function() {
            if (!this.selected) {
                this.$refs['uploader-'+this.id].click();
            }
        },
        view: function(event) {
            if (!this.image) {
                return false;
            }
            this.loading = true;
            let input = event.target;
            var image = document.getElementById(this.id);
            var instance = this;
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    image.src = e.target.result;
                    let extension = input.files[0].name.split('.');
                    // Gets the details for the specific image to do appropraite comparisons
                    setTimeout(() => {
                        instance.details = {
                            name: input.files[0].name,
                            type: input.files[0].type,
                            extension: extension[extension.length - 1],
                            width: image.naturalWidth,
                            height: image.naturalHeight,
                            ratio: instance.reduce((image.naturalWidth / image.naturalHeight * 10), 10),
                            size: input.files[0].size,
                        };
                    }, 50);
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
            this.value = '';
            let image = document.getElementById(this.id);
            image.src = this.default;
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
        errors: { type: [Array, Object], default: [] },
        disabled: { type: Boolean, default: false },
        placeholder: { type: String, default: '' },
        hideDetails: { type: Boolean, default: false },
    }
}
</script>
<style type="text/scss">
    .ee-form-input {
        .ee-form-uploader {
            max-width: 500px;
            position: relative;
            cursor: pointer;
            border: 1px dashed red;
            padding: 4px;
            border-radius: 5px;
            .ee-form-image {
                width: 100%;
            }
            .ee-form-upload-text {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                text-align: center;
                font-weight: bold;
            }
            .ee-form-upload-clear {
                background: white;
                border-radius: 50px;
                position: absolute;
                top: 12px;
                right: 12px;
                height: 25px;
                width: 25px;
                font-size: 25px;
                line-height: 25px;
                text-align: center;
                .material-icons {
                    vertical-align: top;
                }
            }
        }
    }
</style>