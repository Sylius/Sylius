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
        $('.variant-table-toggle i.glyphicon').on('click', function(e) {
            $(this).toggleClass('glyphicon-chevron-down glyphicon-chevron-up');
            $(this).parent().parent().find('table tbody').toggle();
        });
        $('.datepicker').datepicker({});

        // Just for usability, Auto past taxon name into name_en input.
        var taxonNameEn = $('#sylius_taxon_nameEn');
        if(taxonNameEn.length) {
            var taxonName = $('#sylius_taxon_name');
            if(!taxonNameEn.val()) {
                taxonName.keyup(function() {
                    taxonNameEn.val(taxonName.val());
                });
            }

            taxonNameEn.blur(function() {
               if(!taxonNameEn.val()) {
                   taxonNameEn.val(taxonName.val());
               }
            });
        }
    });
})( jQuery );
