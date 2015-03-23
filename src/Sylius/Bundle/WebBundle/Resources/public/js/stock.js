function stockToggle()
{
	if (0 == $('#sylius_product_masterVariant_stock_manage_stock:checked, sylius_product_variant_stock_manage_stock:checked').length)
	{
		$('.stockValues')
			.fadeTo('slow', 0.4)
			.find('input')
				.each(function() {
					$(this).prop('disabled', true);
				})
		;
	} else {
		$('.stockValues')
			.fadeTo('slow', 1)
			.find('input')
				.each(function() {
					$(this).prop('disabled', false);
				})
		;		
	}
}

stockToggle();

$('#sylius_product_masterVariant_stock_manage_stock, sylius_product_variant_stock_manage_stock:checked').click(function() {
	stockToggle();
})
