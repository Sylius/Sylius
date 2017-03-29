/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function($) {
    $(document).ready(function () {
        $('#sylius_product_variant_pricingCalculator').handlePrototypes({
            'prototypePrefix': 'sylius_product_variant_pricingCalculator',
            'containerSelector': '#sylius_calculator_container'
        });

        $('#sylius_customer_createUser').change(function () {
            $('#user-form').toggle();
        });

        $('.sylius-autocomplete').autoComplete();

        $('.product-select.ui.fluid.multiple.search.selection.dropdown').productAutoComplete();
        $('div#attributeChoice > .ui.dropdown.search').productAttributes();

        $('table thead th.sortable').on('click', function () {
            window.location = $(this).find('a').attr('href');
        });

        $('.sylius-update-product-taxons').moveProduct($('.sylius-product-taxon-position'));
        $('.sylius-update-product-variants').moveProductVariant($('.sylius-product-variant-position'));
        $('.sylius-taxon-move-up').taxonMoveUp();
        $('.sylius-taxon-move-down').taxonMoveDown();

        $('#sylius_shipping_method_calculator').handlePrototypes({
            'prototypePrefix': 'sylius_shipping_method_calculator_calculators',
            'containerSelector': '.configuration'
        });

        $('#actions a[data-form-collection="add"]').on('click', function () {
            setTimeout(function(){
                $('select[name^="sylius_promotion[actions]"][name$="[type]"]').last().change();
            }, 50);
        });
        $('#rules a[data-form-collection="add"]').on('click', function () {
            setTimeout(function(){
                $('select[name^="sylius_promotion[rules]"][name$="[type]"]').last().change();
            }, 50);
        });

        $(document).on('collection-form-add', function () {
            $.each($('.sylius-autocomplete'), function (index, element) {
                if ($._data($(element).get(0), 'events') == undefined) {
                    $(element).autoComplete();
                }
            });
        });
        $(document).on('collection-form-update', function () {
            $.each($('.sylius-autocomplete'), function (index, element) {
                if ($._data($(element).get(0), 'events') == undefined) {
                    $(element).autoComplete();
                }
            });
        });

        $('.sylius-tabular-form').addTabErrors();
        $('.ui.accordion').addAccordionErrors();
        $('#sylius-product-taxonomy-tree').choiceTree('productTaxon', true, 1);

        $(document).notification();
        $(document).productSlugGenerator();
        $(document).taxonSlugGenerator();
    });
})(jQuery);
