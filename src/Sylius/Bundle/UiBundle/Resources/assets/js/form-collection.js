/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function ($) {
    'use strict';

    $(document).ready(function() {
        $(document).on('click', 'a[data-collection-button="add"]', function(e) {
            e.preventDefault();

            var collectionContainer = $('#' + $(this).data('collection'));
            var prototype = $('#' + $(this).data('prototype')).data('prototype');
            var item = prototype.replace(/__name__/g, collectionContainer.children('.collection-item').length);
            collectionContainer.append(item);
        });
        $(document).on('click', 'a[data-collection-button="remove"]', function(e) {
            e.preventDefault();

            var item = $(this).closest('.collection-item');
            item.remove();
        });
    });
})(jQuery);
