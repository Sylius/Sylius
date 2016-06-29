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
        $('#actions a[data-form-collection="add"]').on('click', function(){
            setTimeout(function(){
                $('select[name^="sylius_promotion[actions]"][name$="[type]"]').last().change();
            }, 50);
        });
    });
})(jQuery);
