 'use strict';
/**
 * Idle filter used as default.
 *
 * @author    Yohan Blain <yohan.blain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

define(
    ['jquery', 'oro/translator', 'pim/form'],
    function ($, __, BaseForm) {
        return BaseForm.extend({
            /**
             * @returns {String}
             */
            getCode() {
                return 'at-this-level';
            },

            /**
             * @returns {String}
             */
            getLabel() {
                return __('pim_enrich.form.product.tab.attributes.attribute_filter.at_this_level');
            },

            /**
             * @returns {Boolean}
             */
            isVisible() {
                return this.getFormData().meta.attributes_for_this_level;
            },

            /**
             * @param {Object} values
             *
             * @returns {Promise}
             */
            filterValues(values) {
                const valuesToFill = _.pick(values, this.getFormData().meta.attributes_for_this_level);

                return $.Deferred().resolve(valuesToFill).promise();
            }
        });
    }
);
