/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function($) {
    $(document).ready(function() {
        $('#sylius_product_variant_pricingCalculator').handlePrototypes({
            'prototypePrefix': 'sylius_product_variant_pricingCalculator',
            'containerSelector': '#sylius_calculator_container'
        });

        $('#sylius_customer_create_user').change(function(){
            $('#user-form').toggle();
        });

        $('.taxon-select.ui.fluid.search.selection.dropdown').taxonAutoComplete('get taxons');
        $('.product-select.ui.fluid.multiple.search.selection.dropdown').productAutoComplete('get products');
        $('div#attributeChoice > .ui.dropdown.search').productAttributes();

        $('table thead th.sortable').on('click', function () {
            window.location = $(this).find('a').attr('href');
        });

        $('.sylius-update-product-taxons').moveProduct($('.sylius-product-taxon-position'));
        $('.sylius-taxon-move-up').taxonMoveUp();
        $('.sylius-taxon-move-down').taxonMoveDown();

        $('#sylius_shipping_method_calculator').handlePrototypes({
            'prototypePrefix': 'sylius_shipping_method_calculator_calculators',
            'containerSelector': '.configuration'
        });

        $('#actions a[data-form-collection="add"]').on('click', function(){
            setTimeout(function(){
                $('select[name^="sylius_promotion[actions]"][name$="[type]"]').last().change();
            }, 50);
        });
    });
})(jQuery);
