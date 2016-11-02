$(document).ready(function() {
    var timeout;

    $('[name*="sylius_product[translations]"][name*="[name]"]').on('input', function() {
        clearTimeout(timeout);
        $element = $(this);

        timeout = setTimeout(function() {
            updateSlug($element);
        }, 1000);
    });

    $('#toggle-slug-modification').on('click', function(e) {
        e.preventDefault();
        toggleSlugModification($(this), $(this).siblings('input'));
    });
});

function updateSlug($element) {
    $form = $element.parents('form');
    $slugInput = $element.parents('.content').find('[name*="[slug]"]');

    if ('disabled' == $slugInput.attr('disabled')) {
        return;
    }

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

function toggleSlugModification($button, $slugInput) {
    if ($slugInput.attr('disabled')) {
        $slugInput.removeAttr('disabled');
    } else {
        $slugInput.attr('disabled', 'disabled');
    }

    $currentText = $button.attr('data-toggle-text');
    $button.attr('data-toggle-text', $button.text());
    $button.text($currentText);
}
