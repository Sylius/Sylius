$(document).ready(function() {
    var timeout;

    $('[name*="sylius_taxon[translations]"][name*="[name]"]').on('input', function() {
        clearTimeout(timeout);
        $element = $(this);

        timeout = setTimeout(function() {
            updateSlug($element);
        }, 1000);
    });

    $('#toggle-taxon-slug-modification').on('click', function(e) {
        e.preventDefault();
        toggleSlugModification($(this), $(this).siblings('input'));
    });
});

function updateSlug($element) {
    $slugInput = $element.parents('.content').find('[name*="[slug]"]');
    $loadableParent = $slugInput.parents('.field.loadable');

    if ('readonly' == $slugInput.attr('readonly')) {
        return;
    }

    $loadableParent.addClass('loading');

    if ('' != $slugInput.attr('data-parent') && undefined != $slugInput.attr('data-parent')) {
        $data = { name: $element.val(), parentId: $slugInput.attr('data-parent') };
    } else if ($('#sylius_taxon_parent').length > 0 && $('#sylius_taxon_parent').is(':visible') && '' != $('#sylius_taxon_parent').val()) {
        $data = { name: $element.val(), parentId: $('#sylius_taxon_parent').val() };
    } else {
        $data = { name: $element.val() };
    }

    $.ajax({
        type: "GET",
        url: $slugInput.attr('data-url'),
        data: $data,
        dataType: "json",
        accept: "application/json",
        success: function(data) {
            $slugInput.val(data.slug);
            if ($slugInput.parents('.field').hasClass('error')) {
                $slugInput.parents('.field').removeClass('error');
                $slugInput.parents('.field').find('.sylius-validation-error').remove();
            }
            $loadableParent.removeClass('loading');
        }
    });
}

function toggleSlugModification($button, $slugInput) {
    if ($slugInput.attr('readonly')) {
        $slugInput.removeAttr('readonly');
        $button.html('<i class="unlock icon"></i>');
    } else {
        $slugInput.attr('readonly', 'readonly');
        $button.html('<i class="lock icon"></i>');
    }
}
