/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function initializeAjaxForm(successMessage, successHeader, messageHolderSelector) {
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
                appendFlash(successMessage, successHeader, messageHolderSelector);
            },
            error: function(xhr, textStatus, errorThrown) {
                renderErrors(xhr, form);
            }
        });
    });
}

function renderErrors(xhr, form) {
    clearErrors(form);

    $.each(xhr.responseJSON.errors.children, function(key, value) {
        var error = getError(value);
        if (error != null) {
            $('#reviewForm').addClass('error').prepend('<div class="ui error message"><p>' + error +'</p></div>');
        }
    });
}

function getError(value)
{
    if ('errors' in value) {
        return value.errors.join(', ');
    } else if ('children' in value) {
        var error = null;
        for (child in value.children) {
            error = getError(value.children[child]);
        }
        return error;
    }

    return null;
}

function clearErrors(form) {
    form.find('.error.message').remove();
    form.find('.error').removeClass('error');
}

function parseFormToJson(form) {
    var formJson = {};
    $.each(form.serializeArray(), function(index, field) {
        var name = field.name.replace('sylius_product_review[', '').replace(']', '');
        formJson[name] = field.value || '';
    });

    return formJson;
}

function appendFlash(successMessage, successHeader, messageHolderSelector) {
    messageHolderSelector = messageHolderSelector ? messageHolderSelector : '.flashes';
    var html = '<div class="ui icon positive message">' +
                    '<i class="close icon"></i>' +
                    '<i class="checkmark" icon"></i>' +
                    '<div class="content">' +
                        '<div class="header">' +
                            successHeader +
                        '</div>' +
                        '<p>' + successMessage + '</p>' +
                    '</div>' +
                '</div>';

    $(messageHolderSelector).html(html);
}

function completeRequest(form) {
    form[0].reset();
    clearErrors(form);
    $("html, body").animate({ scrollTop: 0 }, "slow");
}
