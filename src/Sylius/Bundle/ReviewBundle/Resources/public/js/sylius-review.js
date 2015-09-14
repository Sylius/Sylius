/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$(document).ready(function(){
    $("#reviewForm").submit(function(e){
        e.preventDefault();
        var form = $(this);

        $.ajax({
            type: "POST",
            url: location.href+"/product_review",
            data: $(this).serialize(),
            dataType: "json",
            success: function(data) {
                if (data == 'success') {
                    location.reload();
                } else {
                    renderErrorsOnProperFields(data, form);
                }
            }
        });
    });
});

function renderErrorsOnProperFields(data, form) {
    clearErrors(form);

    $.each(data, function(key, value){
        var element = form.find('#sylius_product_review_'+key.replace('data.', ''));

        element.siblings('.form-error').remove();
        element.parent().append('<span class="help-block form-error">'+value+'</span>');
        element.parents('.form-group').addClass('has-error');
    });
}

function clearErrors(form) {
    form.find('.form-error').remove();
    form.find('.has-error').removeClass('has-error');
}