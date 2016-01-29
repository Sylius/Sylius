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

    function increaseAttributesNumber() {
        var currentIndex = parseInt(getNextIndex());
        $('#attributes .collection-list').attr('data-length', currentIndex+1);
    }

    function getNextIndex() {
        return $('#attributes .collection-list').attr('data-length');
    }

    function modifyModalOnItemDelete() {
        $('a[data-form-collection="delete"]').on('mousedown', function(){
            setTimeout(function(){
                controlAttributesList();
            }, 500);
        });
    }

    function controlAttributesList() {
        $.each($('#attributes-modal a'), function() {
            $(this).css('display', 'block');
            $(this).find('input').attr('checked', false);
        });
        $.each($('#attributes .collection-item'), function(){
            var usedAttributeId =  $(this).find('input').val();

            $('#attributes-modal').find('input[value="'+usedAttributeId+'"]').parent().css('display', 'none');
        });
    }

    function modifyAttributeForms(data) {
        $.each($(data).find('input,select,textarea'), function(){
            if ($(this).attr('data-name') != null) {
                $(this).attr('name', $(this).attr('data-name'));
            }
        });

        return data;
    }

    function setAttributeChoiceListener() {
        $('#attributeChoice').submit(function(event) {
            event.preventDefault();
            var form = $(this);

            $.ajax({
                type: 'GET',
                url: form.attr('action'),
                data: form.serialize()+'&count='+getNextIndex(),
                dataType: 'html'
            }).done(function(data){
                var finalData = modifyAttributeForms($(data));
                $('#attributes .collection-list').append(finalData);
                $('#attributes-modal').modal('hide');
                modifyModalOnItemDelete();
                increaseAttributesNumber();
            });
        });
    }

    $(document).ready(function(){
        var attributesModal = $('#attributes-modal');
        attributesModal
            .on('shown.bs.modal', function () {
                setAttributeChoiceListener();
            })
            .on('hide.bs.modal', function () {
                $('#attributeChoice').unbind();
            })
            .on('hidden.bs.modal', function() {
                controlAttributesList();
            })
        ;

        controlAttributesList();
        modifyModalOnItemDelete();
    });
})( jQuery );
