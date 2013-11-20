/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

!function($){

    "use strict"

    /**
     * Collection Form plugin
     *
     * @param element
     * @constructor
     */
    var CollectionForm = function (element) {
        this.$element = $(element);
        this.formPrototype = this.$element.data('prototype');
        this.$list = this.$element.find('[data-form-collection="list"]:first')
        this.count = this.$list.children().length;

        this.$element.find('[data-form-collection="add"]:first')
            .on('click', $.proxy(this.addItem, this));

        $(document).on(
            'click',
            '[data-form-collection="delete"]',
            $.proxy(this.deleteItem, this)
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

            var prototype = this.formPrototype.replace(
                /__name__/g,
                this.count
            );

            this.$list.append(prototype);
            this.count = this.count + 1;

            $(document).trigger('collection-form-add', [this.$list.children().first()]);
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
        }
    }

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
    }

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