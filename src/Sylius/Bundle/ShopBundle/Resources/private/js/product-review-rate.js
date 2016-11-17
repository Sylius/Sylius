$(document).ready(function () {
    $('.star.rating').rating({
        fireOnInit: true,
        onRate: function(value) {
            $("[name='sylius_review[rating]']:checked").removeAttr('checked');
            $("#sylius_review_rating_"+(value-1)).attr('checked', 'checked');
        }
    });
});
