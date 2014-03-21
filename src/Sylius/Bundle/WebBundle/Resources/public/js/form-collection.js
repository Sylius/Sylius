/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require(["jquery", "jquery.bootstrap"], function ($) {
    'use strict';

    $(document).ready(function() {
        $(document).on('click', 'a[data-collection-button="add"]', function(e) {
            e.preventDefault();
            var collectionContainer = $('#' + $(this).data('collection'));
            var prototype = $('#' + $(this).data('prototype')).data('prototype');
            var item = prototype.replace(/__name__/g, collectionContainer.children().length);
            collectionContainer.append(item);
        });

        $(document).on('click', 'a[data-collection-button="delete"]', function(e) {
            e.preventDefault();
            var item = $(this).closest('.' + $(this).data('collection') + '-' + $(this).data('collection-item'));
            item.remove();
        });
    });
});
