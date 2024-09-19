<template>
    <div class="form-floating ee-form-input">
        <div v-if="image" class="ee-form-uploader" @click="upload">
            <img v-show="!multiple || total <= 1" :src="default" :id="id" class="ee-image ee-image-preview" :class="{ 'ee-image-cursor': !selected }">
            <div :id="id + '-container'" class="ee-image-container" :class="{ 'scroll': total > 1 }"></div>
            <div v-if="!selected" class="ee-form-upload-text">
                Browse for File Upload
                <ul v-if="!hideDetails && errorList.length > 0" class="form-errors ee-form-errors mb-2">
                    <li v-for="(error, i) in errorList" :key="id+'-error-'+i" class="form-error ee-form-error">{{ error }}</li>
                </ul>
            </div>
            <div v-else-if="!multiple || total <= 1" class="ee-form-upload-clear" @click="clear"><span class="material-icons">close</span></div>
        </div>
        <div v-if="description != null" class="ee-upload-description">{{ description }}</div>
        <input v-show="false" type="file" :ref="'uploader-'+id" :id="'uploader-'+id" @change="change" :multiple="multiple">
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
            total: 0,
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
            var container = document.getElementById(this.id+'-container');
            var previewImage = document.getElementById(this.id);
            var _this = this;
            if (input.files && input.files.length > 0) {
                this.total = input.files.length;
                let reader = new FileReader();
                function readFile(index) {
                    if (index >= input.files.length) return;
                    reader.onload = function(e) {
                        setTimeout(() => {
                            // Creates hte container for the clear and image
                            let imageContainer = document.createElement('div');
                            imageContainer.className = 'ee-form-multiple-image-container';
                            if (_this.thumbnail) {
                                // Adds an event to allow the user to select a new thumbnail without re-uploading
                                imageContainer.addEventListener('click', (e) => {
                                    // Makes sure not to mix up click functionality
                                    if (e.target.className.indexOf('ee-form-upload-clear') != -1 ||
                                        e.target.className.indexOf('material-icons') != -1) {
                                        return;
                                    }
                                    let containers = document.querySelectorAll('.ee-form-multiple-image-container');
                                    const target = e.target.closest('.ee-form-multiple-image-container');
                                    let index = Array.from(containers).indexOf(target);
                                    document.getElementById(_this.id+'-container').prepend(target);
                                    // Removes the current file from it's current location and then adds it to the front
                                    let thumbnail = _this.value.splice(index, 1);
                                    _this.value.unshift(thumbnail[0]);
                                });
                            }
                            // Creates the actual image to be displayed
                            let image = document.createElement('img');
                            image.className = ['ee-image',
                                index == 0 && _this.thumbnail ? ' ee-thumbnail-image' : '',
                                _this.thumbnail ? ' ee-thumbnail-selector' : '',
                            ].join(' ');
                            image.src = e.target.result;
                            // Creates the icon to be clicked
                            let icon = document.createElement('span');
                            icon.className = 'material-icons';
                            icon.textContent = 'close';
                            // Creates the icon to be used to clear
                            let clear = document.createElement('span');
                            clear.className = 'ee-form-upload-clear ' + index;
                            clear.appendChild(icon);
                            clear.addEventListener('click', (e) => {
                                let buttons = document.querySelectorAll('.ee-form-upload-clear');
                                const target = e.target.closest('.ee-form-upload-clear');
                                let index = Array.from(buttons).indexOf(target);
                                // Removes the image
                                target.closest('.ee-form-multiple-image-container').remove();
                                _this.value.splice(index, 1);
                                _this.total--;
                                if (_this.total == 1) {
                                    // Gets the other containers to find the last image
                                    let containers = document.getElementsByClassName('ee-form-multiple-image-container');
                                    if (containers.length == 1) {
                                        let image = containers[0].querySelector('img');
                                        let previewImage = document.getElementById(_this.id);
                                        containers[0].remove();
                                        previewImage.src = image.src;
                                    }
                                } else if (_this.total == 0) {
                                    _this.selected = false;
                                }
                            });
                            let extension = input.files[index].name.split('.');
                            // Gets the details for the specific image to do appropraite comparisons
                            try {
                                let details = {
                                    name: input.files[index].name,
                                    type: input.files[index].type,
                                    extension: extension[extension.length - 1],
                                    width: image.naturalWidth,
                                    height: image.naturalHeight,
                                    ratio: _this.reduce((image.naturalWidth / image.naturalHeight * 10), 10),
                                    size: input.files[index].size,
                                };
                                // Checks to make sure the type is correctly set
                                if (_this.accept.split(',').indexOf(details.type) == -1) {
                                    throw new Error(`The file\'s extension must be one of the following types: ${_this.accept}.`);
                                }
                                // Checks to make sure the image uploaded is a square
                                if (_this.square && image.naturalHeight != image.naturalWidth) {
                                    throw new Error('The image selected must be a square.');
                                }
                                // Checks to make sure the ratio is correctly uploaded
                                if (_this.ratio != details.ratio && _this.ratio != null) {
                                    throw new Error(`The image\'s ratio does not match ${_this.ratio}.`);
                                }
                                // Determines how the image should be displayed
                                if (_this.multiple && _this.total > 1) {
                                    imageContainer.appendChild(image);
                                    imageContainer.appendChild(clear);
                                    container.appendChild(imageContainer);
                                } else {
                                    previewImage.src = image.src;
                                }
                            } catch ({ name, message }) {
                                if (_this.total == 1) {
                                    _this.clear();
                                } else {
                                    _this.value.splice(index, 1);
                                    _this.total--;
                                }
                                _this.errorList = [message];
                            }
                            readFile(index+1);
                        }, 50 * (index + 1));
                    }
                    reader.readAsDataURL(input.files[index]);
                }
                readFile(0);
            }
            setTimeout(() => {
                this.loading = false;
            }, 1000);
        },
        change: function(event) {
            this.selected = true;
            this.view(event);
            this.$emit('update:modelValue', Array.from(event.target.files));
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
        modelValue: { type: [File, String, Array], default: '' },
        label: { type: String, default: null },
        accept: { type: String, default: 'image/png,image/jpeg,image/jpg' },
        image: { type: Boolean, default: true },
        square: { type: Boolean, default: true },
        ratio: { type: String, default: null },
        multiple: { type: Boolean, default: false },
        errors: { type: [Array, Object], default: [] },
        thumbnail: { type: Boolean, default: false },
        description: { type: String, default: null },
        disabled: { type: Boolean, default: false },
        placeholder: { type: String, default: '' },
        hideDetails: { type: Boolean, default: false },
    }
}
</script>