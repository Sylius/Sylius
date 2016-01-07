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
        $('input[class^="autocomplete"]').select2({
            ajax: {
                dataType: "json",
                url:  $('input[class^="autocomplete"]').attr('src'),
                results: function (data) {
                    return {results: data};
                }
            }
        });
    });
})( jQuery );
