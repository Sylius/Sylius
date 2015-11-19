/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function modifyAttributeForms(data) {
    $.each(data.find('.form-group'), function(){
        console.log($(this).find('label'));
        console.log($(this).find('form'));
    });
}

function setAttributeChoiceListener() {
    $('#attributeChoice').submit(function(event) {
        event.preventDefault();
        var form = $(this);
        $.ajax({
            type: 'GET',
            url: form.attr('action'),
            data: form.serialize(),
            dataType: 'html'
        }).done(function(data){
            modifyAttributeForms(data);
            $('#attributes-modal').modal('hide');
        //    $('#attributes').prepend(data);
        });
    });
}

(function ($) {
    'use strict';

    $(document).ready(function(){
        var attributesModal = $('#attributes-modal');
        attributesModal.on('shown.bs.modal', function () {
            console.log('show');
            setAttributeChoiceListener();
        });
        attributesModal.on('hide.bs.modal', function () {
            $('#attributeChoice').unbind();
        });
    });
})( jQuery );
