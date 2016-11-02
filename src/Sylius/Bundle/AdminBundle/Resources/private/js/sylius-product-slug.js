$(document).ready(function() {
    var timeout;

    $('[name*="sylius_product[translations]"][name*="[name]"]').on('input', function() {
        clearTimeout(timeout);
        $element = $(this);

        timeout = setTimeout(function() {
            updateSlug($element);
        }, 1000);
    });
});

function updateSlug($element) {
    $form = $element.parents('form');
    $slugInput = $element.parents('.content').find('[name*="[slug]"]');

    $form.addClass('loading');

    $.ajax({
        type: "GET",
        url: $slugInput.attr('data-url'),
        data: { name: $element.val() },
        dataType: "json",
        accept: "application/json",
        success: function(data) {
            $slugInput.val(data.slug);
            $form.removeClass('loading');
        }
    });
}
