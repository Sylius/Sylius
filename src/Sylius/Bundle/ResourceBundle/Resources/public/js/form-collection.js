/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
!function($){

    "use strict";

    /**
     * Collection Form plugin
     *
     * @param element
     * @constructor
     */
    var CollectionForm = function (element) {
        this.$element = $(element);
        this.$list = this.$element.find('[data-form-collection="list"]:first');
        this.count = this.$list.children().length;

        this.$element.on(
            'click',
            '[data-form-collection="add"]:first',
            $.proxy(this.addItem, this)
        );

        this.$element.on(
            'click',
            '[data-form-collection="delete"]',
            $.proxy(this.deleteItem, this)
        );

        this.$element.on(
            'change',
            '[data-form-collection="update"]',
            $.proxy(this.updateItem, this)
        );

        $(document).on(
            'change',
            '[data-form-prototype="update"]',
            $.proxy(this.updatePrototype, this)
        );
    }
    CollectionForm.prototype = {
        constructor : CollectionForm,

        /**
         * Add a item to the collection.
         * @param event
         */
        addItem: function (event) {
            event.preventDefault();

            var prototype = this.$element.data('prototype');

            prototype = prototype.replace(
                /__name__/g,
                this.count
            );

            this.$list.append(prototype);
            this.count = this.count + 1;

            $(document).trigger('collection-form-add', [this.$list.children().first()]);
        },

        /**
         * Update item from the collection
         */
        updateItem: function (event) {
            event.preventDefault();
            var $element = $(event.currentTarget),
                url = $element.data('form-url'),
                value = $element.val(),
                $container = $element.closest('[data-form-collection="item"]'),
                index = $container.data('form-collection-index'),
                position = $container.data('form-collection-index');
            if (url) {
                $container.load(url, {'id' : value, 'position' : position});
            } else {
                var prototype = this.$element.find('[data-form-prototype="'+ value +'"]').val();

                prototype = prototype.replace(
                    /__name__/g,
                    index
                );

                $container.replaceWith(prototype);
            }
            $(document).trigger('collection-form-update', [this.$list.children().first()]);
        },

        /**
         * Delete item from the collection
         * @param event
         */
        deleteItem: function (event) {
            event.preventDefault();

            $(event.currentTarget)
                .closest('[data-form-collection="item"]')
                .remove();

            $(document).trigger('collection-form-delete', [$(event.currentTarget)]);
        },

        /**
         * Update the prototype
         * @param event
         */
        updatePrototype: function (event) {
            var $target = $(event.currentTarget);
            var prototypeName = $target.val();

            if (undefined !== $target.data('form-prototype-prefix')) {
                prototypeName = $target.data('form-prototype-prefix') + prototypeName;
            }

            this.$list.html('');

            this.$element.data(
                'prototype',
                this.$element.find('[data-form-prototype="'+ prototypeName +'"]').val()
            );
        }
    };

    /*
     * Plugin definition
     */

    $.fn.CollectionForm = function (option) {
        return this.each(function () {
            var $this = $(this);
            var data = $this.data('collectionForm');
            var options = typeof option == 'object' && option;

            if (!data) {
                $this.data(
                    'collectionForm',
                    (data = new CollectionForm(this, options))
                )
            }
        })
    };

    $.fn.CollectionForm.Constructor = CollectionForm;

    /*
     * Apply to standard CollectionForm elements
     */

    $(document).on('collection-form-add', function(e, addedElement) {
        $(addedElement).find('[data-form-type="collection"]').CollectionForm();
        $(document).trigger('dom-node-inserted', [$(addedElement)]);
    });

    $('[data-form-type="collection"]').CollectionForm();
}(jQuery);