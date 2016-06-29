/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//; here is caused of twitter bootstrap do not have ; at the end of file
;(function ( $ ) {
    'use strict';

    $(document).ready(function() {
        $(document).on('click', 'a[data-collection-button="add"]', function(e) {
            e.preventDefault();
            var collectionContainer = $('#' + $(this).data('collection'));
            var prototype = $('#' + $(this).data('prototype')).data('prototype');

            // Check if an element with this ID already exists.
            // If it does, increase the count by one and try again
            var id = prototype.match(/input.*?id="(.*?)"/);
            var count = 0;
            while ($('#' + id[1].replace(/__name__/g, count)).length > 0) {
                count++;
            }

            var item = prototype.replace(/__name__/g, count);
            collectionContainer.append(item);
        });

        $(document).on('click', 'a[data-collection-button="delete"]', function(e) {
            e.preventDefault();
            var item = $(this).closest('.' + $(this).data('collection') + '-' + $(this).data('collection-item'));
            item.remove();
        });
    });
})( jQuery );
