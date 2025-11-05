'use strict';
/**
 * Media field
 */
define([
        'jquery',
        'pim/form/common/fields/field',
        'underscore',
        'routing',
        'referencedata/template/field/media-field',
        'pim/common/property',
        'oro/mediator',
        'oro/messenger',
        'pim/media-url-generator',
        'jquery.slimbox'
    ],
    function ($, Field, _, Routing, fieldTemplate, propertyAccessor, mediator, messenger, MediaUrlGenerator) {

        return Field.extend({
            fieldTemplate: _.template(fieldTemplate),
            ready: true,
            events: {
                'change input': function (event) {
                    this.errors = [];
                    this.updateModel(event);
                },
                'click  .open-media': 'previewImage',
                'click .clear-field': 'clearField'
            },
            uploadContext: {},

            renderInput: function (context) {
                return this.fieldTemplate(_.extend(context, {
                    value: this.getModelValue()
                }));
            },

            getTemplateContext: function () {
                return Field.prototype.getTemplateContext.apply(this, arguments)
                    .then(function (templateContext) {
                        templateContext.inUpload = !this.isReady();
                        templateContext.mediaUrlGenerator = MediaUrlGenerator;

                        return templateContext;
                    }.bind(this));
            },

            updateModel: function (event) {
                if (!this.isReady()) {
                    console.log('not ready');
                }

                var input = event.target;
                if (!input || 0 === input.files.length) {
                    return;
                }
                var formData = new FormData();
                formData.append('file', input.files[0]);
                this.setReady(false);

                $.ajax({
                    url: Routing.generate('pim_enrich_media_rest_post'),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    xhr: function () {
                        var myXhr = $.ajaxSettings.xhr();
                        if (myXhr.upload) {
                            myXhr.upload.addEventListener('progress', this.handleProcess.bind(this), false);
                        }

                        return myXhr;
                    }.bind(this)
                })
                    .done(function (data) {
                        this.setUploadContextValue(data);
                    }.bind(this))
                    .fail(function (xhr) {
                        var message = xhr.responseJSON && xhr.responseJSON.message ?
                            xhr.responseJSON.message :
                            _.__('pim_enrich.entity.product.error.upload');
                        messenger.enqueueMessage('error', message);
                    })
                    .always(function () {
                        this.$('> .akeneo-media-uploader-field .progress').css({opacity: 0});
                        this.setReady(true);
                        this.uploadContext = {};
                    }.bind(this));
            },

            setCurrentValue: function(value){
                const data = this.getFormData();
                propertyAccessor.updateProperty(data, this.fieldName, value);

                this.setData(data);
                this.render();
            },

            clearField: function (event) {
                var value = {
                    filePath: null,
                    originalFilename: null
                };

                this.setCurrentValue(value);
            },

            handleProcess: function (e) {
                this.$('> .akeneo-media-uploader-field .progress').css({opacity: 1});
                this.$('> .akeneo-media-uploader-field .progress .bar').css({
                    width: ((e.loaded / e.total) * 100) + '%'
                });
            },

            getCurrentValue: function () {
                return propertyAccessor.accessProperty(this.getFormData(),this.fieldName);
            },

            previewImage: function () {
                var mediaUrl = MediaUrlGenerator.getMediaShowUrl(this.getCurrentValue().filePath, 'preview');
                if (mediaUrl) {
                    $.slimbox(mediaUrl, '', {overlayOpacity: 0.3});
                }
            },

            setUploadContextValue: function (value) {
                this.setCurrentValue(value);

                mediator.trigger('pim_enrich:form:entity:post_update');
            },
            /**
             * Set this field as ready
             *
             * @param {boolean} ready
             */
            setReady: function (ready) {
                this.ready = ready;
            },

            /**
             * Return whether this field is ready
             *
             * @returns {boolean}
             */
            isReady: function () {
                return this.ready;
            }
        });
    }
);
