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

    $(document).ready(function() {
        var $propertyType = $('#sylius_property_type'),
            choiceType = $propertyType.data('config-choice'),
            prototype = $propertyType.data('config-prototype'),
            $container = $('[data-config-form="container"]'),
            $main = $('[data-config-form="main"]');

        $propertyType.on('change', function(){
            var choiceValue = $(this).val();

            if (choiceType == choiceValue) {
                $container.removeClass('hide').addClass('col-md-6');
                $main.addClass('col-md-6');

                $('[data-form-type="collection"]').CollectionForm();
            } else {
                $container.addClass('hide').removeClass('col-md-6');
                $main.removeClass('col-md-6');
            }
        });
    });
})( jQuery );
