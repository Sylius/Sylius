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

        $('.sylius-different-billing-address-trigger').click(function() {
            $('#sylius-billing-address-container').toggleClass('hidden');
        });

        $('.search-panel .dropdown-menu').find('a').click(function (e) {
            e.preventDefault();
            var param = $(this).attr("href").replace("#", "");
            var concept = $(this).text();
            $('.search-panel span#search_concept').text(concept);
            $('.input-group #search_param').val(param);
        });

    });

})( jQuery );
