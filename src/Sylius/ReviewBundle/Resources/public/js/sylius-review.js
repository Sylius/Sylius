/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function initializeAjaxForm(successMessage, messageHolderSelector) {
    $("#reviewForm").submit(function(e) {
        e.preventDefault();
        var form = $(this);

        $.ajax({
            type: "POST",
            url: form.attr('action'),
            data: parseFormToJson(form),
            dataType: "json",
            accept: "application/json",
            success: function(data, textStatus, xhr) {
                completeRequest(form);
                appendFlash(successMessage, messageHolderSelector);
            },
            error: function(xhr, textStatus, errorThrown) {
                renderErrors(xhr, form);
            }
        });
    });
}

function renderErrors(xhr, form) {
    clearErrors(form);

    $.each(xhr.responseJSON.errors.errors, function(key, value) {
        $('div.panel-body').addClass('has-error').prepend('<span class="help-block form-error">' + value + '</span>');
    });
}

function clearErrors(form) {
    form.find('.form-error').remove();
    form.find('.has-error').removeClass('has-error');
}

function parseFormToJson(form) {
    var formJson = {};
    $.each(form.serializeArray(), function(index, field) {
        var name = field.name.replace('sylius_product_review[', '').replace(']', '');
        formJson[name] = field.value || '';
    });

    return formJson;
}

function appendFlash(successMessage, messageHolderSelector) {
    messageHolderSelector = messageHolderSelector ? messageHolderSelector : '#flashes-container';

    $(messageHolderSelector).html('<div class="alert alert-success"><a class="close" data-dismiss="alert" href="#">×</a>' + successMessage + '</div>');
}

function completeRequest(form) {
    form[0].reset();
    clearErrors(form);
    $("html, body").animate({ scrollTop: 0 }, "slow");
}
