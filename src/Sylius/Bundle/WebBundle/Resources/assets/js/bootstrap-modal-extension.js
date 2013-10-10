/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 * @author aRn0D (Arnaud Langlade) <arn0d.dev at gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function ($) {
    'use strict';

    var originalShow = $.fn.modal.Constructor.prototype.show;

    $.extend($.fn.modal.Constructor.prototype, {
        show: function() {
            if(this.getType() == 'confirmation') {
                var primaryButton = this.getPrimaryButton();
                var clickableElement = this.getClickableElement();

                this.setBodyElement(this.getDomConfig('message'));

                if (clickableElement.is('a')) {
                    primaryButton.attr('href', clickableElement.attr('href'));
                } else {
                    var destination = this.getDomConfig('destination');
                    var form;

                    if (destination == null) {
                        form = clickableElement.closest('form')
                    } else {
                        form = $(destination);
                    }

                    primaryButton.on("click", function (event) {
                        form.submit();
                    });
                }
            }

            originalShow.apply(this, arguments);
        },

        /**
         * Get the type of the modal
         * @returns string
         */
        getType: function() {
            return this.options.toggle;
        },

        /**
         * Get the html element where the user has clicked to open the popup
         * @returns {*}
         */
        getClickableElement: function() {
            return this.options.clickableElement;
        },

        /**
         * Get the data attribute destination on the clickable element
         * @returns {*}
         */
        getDomConfig: function (key) {
            var value = this.getClickableElement().data(key);

            if(value == undefined || value == "") {
                return null;
            }

            return value;
        },

        /**
         * Get the body element
         * @returns {*}
         */
        getBodyElement: function () {
            var $body = this.$element.find('.modal-body');

            if ($body.length != 1) {
                $body = this.$element;
            }

            return $body;
        },

        /**
         * Set the body of the modal
         *
         * @param html
         * @returns {*}
         */
        setBodyElement: function (html) {
            this.getBodyElement().html(html);

            return this;
        },

        /**
         * Get the html H tag.
         * @returns {*}
         */
        getTitleElement: function() {
            return this.$element.find('.modal-header h3');
        },

        /**
         * Set the title of the modal
         * @param html
         * @returns {*}
         */
        setTitleElement: function (html) {
            this.getTitleElement().html(html);

            return this
        },

        /**
         * Get the primary button (used to confirm action)
         * @returns {*}
         */
        getPrimaryButton: function () {
            return this.$element.find('.modal-footer [data-modal-action="confirm"]');
        }
    });

    $(document).ready(function() {
        $(document.body).on(
            'click.sylius-modal.data-api',
            '[data-toggle="confirmation"]',
            function (e) {
                e.preventDefault();

                var $this = $(this),
                    options = $this.data(),
                    modalElement = $(options.target),
                    data = modalElement.data('modal');

                if (modalElement.length) {
                    if (!data) {
                        options = $.extend(modalElement.data(), options);
                    }

                    options.clickableElement = $this;
                    modalElement.modal(options);
                }
            }
        );
    });
})(jQuery);