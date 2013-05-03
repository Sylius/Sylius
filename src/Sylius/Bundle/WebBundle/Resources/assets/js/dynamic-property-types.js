$(document).ready(function() {
    'use strict';

    $('a[data-collection-button="add"]').on('click', function (e) {
        var collectionContainer = $('#' + $(this).data('collection'));
        var lastElementNumber = (collectionContainer.children().length) - 1;
        $('#sylius_product_properties_' + lastElementNumber + ' .property-chooser').handlePrototypes({
            prototypePrefix: 'property-prototype',
            prototypeElementPrefix: '',
            containerSelector: '#sylius_product_properties_' + lastElementNumber + ' .control-group:last .controls'
        });
        $('#sylius_product_properties_' + lastElementNumber + ' .property-chooser').change(function() {
            $('#sylius_product_properties_' + lastElementNumber + ' .control-group:last .controls input, #sylius_product_properties_' + lastElementNumber + ' .control-group:last .controls select').each(function() {
                this.name = this.name.replace(/__name__/g, lastElementNumber);
                this.id = this.id.replace(/__name__/g, lastElementNumber);
            });
        });
    });
});
