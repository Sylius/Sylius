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
        var deleteButton;

        $('.btn-confirm').click(function(e) {
            e.preventDefault();

            deleteButton = $(this);

            $('#confirmation-modal').modal('show');
        });

        $('#confirmation-modal .btn-danger').click(function(e) {
            e.preventDefault();

            $.ajax({
                type: "DELETE",
                url: deleteButton.parent().attr('action'),
                data: { confirmed: "1" },
                cache: false
            }).done(function(json) {
                $('#confirmation-modal').modal('hide');
                $('#'+json.id).remove();
            });
        });
    });
})( jQuery );
