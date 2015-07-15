/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function ( $ ) {
    'use strict';

    var attributeController = {
        choicesContainer: $('#sylius_form_choice_container'),
        choicesElement: $('#sylius_product_attribute_type'),

        listen: function()
        {
            this.choicesElement.change($.proxy(this.toogleChoices, this));
            this.toogleChoices();
        },

        toogleChoices: function()
        {
            if ('choice' === this.choicesElement.val()) {
                this.choicesContainer.show();
            } else {
                this.choicesContainer.hide();
            }
        }
    };

    $(document).ready(function() {
        attributeController.listen();
    });
})( jQuery );
