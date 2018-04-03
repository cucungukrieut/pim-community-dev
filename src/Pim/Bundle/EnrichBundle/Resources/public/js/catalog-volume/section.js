'use strict';

define(
    [
        'underscore',
        'pim/form',
        'oro/translator',
        'pim/form-builder',
        'pim/template/catalog-volume/section',
        'require-context'
    ],
    function (
        _,
        BaseForm,
        __,
        FormBuilder,
        template,
        requireContext
    ) {
        return BaseForm.extend({
            className: 'AknCatalogVolume-section',
            template: _.template(template),
            hideHint: null,
            events: {
                'click .AknCatalogVolume-remove': 'closeHint',
                'click .open-hint': 'openHint'
            },
            config: {
                warningText: __('catalog_volume.axis.warning'),
                templates: {
                    average_max: 'pim/template/catalog-volume/average-max',
                    count: 'pim/template/catalog-volume/number'
                },
                axes: []
            },
            /**
             * {@inheritdoc}
             */
            initialize: function (options) {
                this.config = Object.assign({}, this.config, options.config);

                return BaseForm.prototype.initialize.apply(this, arguments);
            },

            /**
             * If the hint key is in localStorage, don't show it on first render
             * @return {Boolean}
             */
            hintIsHidden() {
                if (false === this.hideHint) return false;

                return !!localStorage.getItem(this.config.hint.code);
            },

            sectionHasData(sectionData, sectionAxes) {
                return Object.keys(sectionData).filter(field => sectionAxes.indexOf(field) > -1).length > 0;
            },

            /**
             * {@inheritdoc}
             */
            render() {
                const sectionData = this.getRoot().getFormData();
                const sectionAxes = this.config.axes;
                const sectionHasData = this.sectionHasData(sectionData, sectionAxes);

                if (false === sectionHasData) {
                    return;
                }

                this.$el.empty().html(this.template({
                    title: __(this.config.title),
                    hintTitle: __(this.config.hint.title),
                    hintIsHidden: this.hintIsHidden()
                }));

                this.renderAxes(this.config.axes, sectionData);
            },

            /**
             * Generates the html for each axis depending on the type, appends the axis to the axis container
             * @param  {Array} axes An array of field names for each axis
             * @param  {Object} data An object containing data for each axis
             */
            renderAxes(axes, data) {
                axes.forEach(name => {
                    const axis = data[name];

                    if (undefined === axis) return;

                    const typeTemplate = this.config.templates[axis.type];

                    if (undefined === typeTemplate) {
                        throw Error('The axis', name, 'does not have a template for', axis.type);
                    }

                    const template = _.template(requireContext(typeTemplate));

                    const el = template({
                        icon: name.replace(/_/g, '-'),
                        value: axis.value,
                        has_warning: axis.has_warning,
                        title: __(`catalog_volume.axis.${name}`),
                        warningText: this.config.warningText
                    });

                    this.$('.AknCatalogVolume-axisContainer').append(el);
                });
            },

            /**
             * Close the hint box and store the key in localStorage
             */
            closeHint() {
                localStorage.setItem(this.config.hint.code, true);
                this.hideHint = true;
                this.render();
            },

            /**
             * Open the hint box
             */
            openHint() {
                this.hideHint = false;
                this.render();
            }
        });
    }
);
