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
        toogleChoices($('#sylius_property_type').val());
        $('#sylius_property_type').change(function (e) {
            toogleChoices($(this).val());
        });
        $('.delete-link').each(function () {
            var removeLink = $(this);
            removeLink.on('click', function(e) {
                e.preventDefault();

                removeLink.parent().parent().remove();
            });
        });
        $('a[data-collection-button="add"]').on('click', function (e) {
            e.preventDefault();

            var collectionContainer = $('#' + $(this).data('collection'));
            var item = $('#' + $(this).data('collection') + ' .control-group:last-child');
            var removeLink = $('<a class="btn btn-danger sylius_property_choices_' + (collectionContainer.children().length - 1) + '_delete" href="#"><i class="icon-trash"></i></a>');
            removeLink.on('click', function(e) {
                e.preventDefault();

                item.remove();
            });
            item.find('.controls').append(removeLink);
        });
    });

    function toogleChoices(value)
    {
       if (value === 'choice') {
           $('.property-choices-container').show();
       } else {
           $('.property-choices-container').hide();
       }
    }
})( jQuery );
