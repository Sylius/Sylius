<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Sylius\Behat\Element\Admin\Account\ResetElement;
use Sylius\Behat\Element\Admin\CatalogPromotion\FilterElement as CatalogPromotionFilterElement;
use Sylius\Behat\Element\Admin\CatalogPromotion\FormElement as CatalogPromotionFormElement;
use Sylius\Behat\Element\Admin\Channel\DiscountedProductsCheckingPeriodInputElement;
use Sylius\Behat\Element\Admin\Channel\DiscountedProductsCheckingPeriodInputElementInterface;
use Sylius\Behat\Element\Admin\Channel\ExcludeTaxonsFromShowingLowestPriceInputElement;
use Sylius\Behat\Element\Admin\Channel\ExcludeTaxonsFromShowingLowestPriceInputElementInterface;
use Sylius\Behat\Element\Admin\Channel\LowestPriceFlagElement;
use Sylius\Behat\Element\Admin\Channel\LowestPriceFlagElementInterface;
use Sylius\Behat\Element\Admin\Channel\ShippingAddressInCheckoutRequiredElement;
use Sylius\Behat\Element\Admin\Channel\ShopBillingDataElement;
use Sylius\Behat\Element\Admin\Crud\FormElement;
use Sylius\Behat\Element\Admin\Crud\Index\SearchFilterElement;
use Sylius\Behat\Element\Admin\Currency\FormElement as CurrencyFormElement;
use Sylius\Behat\Element\Admin\Customer\FormElement as CustomerFormElement;
use Sylius\Behat\Element\Admin\CustomerGroup\FormElement as CustomerGroupFormElement;
use Sylius\Behat\Element\Admin\ExchangeRate\FormElement as ExchangeRateFormElement;
use Sylius\Behat\Element\Admin\Locale\FormElement as LocaleFormElement;
use Sylius\Behat\Element\Admin\NotificationsElement;
use Sylius\Behat\Element\Admin\ProductOption\FormElement as ProductOptionFormElement;
use Sylius\Behat\Element\Admin\Promotion\FormElement as PromotionFormElement;
use Sylius\Behat\Element\Admin\Promotion\FormElementInterface;
use Sylius\Behat\Element\Admin\PromotionCoupon\FormElement as PromotionCouponFormElement;
use Sylius\Behat\Element\Admin\ShippingMethod\FormElement as ShippingMethodFormElement;
use Sylius\Behat\Element\Admin\TaxCategory\FormElement as TaxCategoryFormElement;
use Sylius\Behat\Element\Admin\Taxon\FormElement as TaxonFormElement;
use Sylius\Behat\Element\Admin\Taxon\ImageFormElement;
use Sylius\Behat\Element\Admin\Taxon\TreeElement;
use Sylius\Behat\Element\Admin\TaxRate\FilterElement as TaxRateFilterElement;
use Sylius\Behat\Element\Admin\TopBarElement;
use Sylius\Behat\Element\Admin\Zone\FormElement as ZoneFormElement;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.behat.element.admin.crud.form', FormElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set('sylius.behat.element.admin.crud.index.search_filter', SearchFilterElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set('sylius.behat.element.admin.account.reset', ResetElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set('sylius.behat.element.admin.channel.shipping_address_in_checkout_required', ShippingAddressInCheckoutRequiredElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set('sylius.behat.element.admin.channel.shop_billing_data', ShopBillingDataElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set('sylius.behat.element.admin.notifications', NotificationsElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set('sylius.behat.element.admin.top_bar', TopBarElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set(CatalogPromotionFormElement::class)
        ->parent('sylius.behat.element')
        ->args([service(AutocompleteHelperInterface::class)])
    ;

    $services
        ->set(CatalogPromotionFilterElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set(TaxRateFilterElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set(DiscountedProductsCheckingPeriodInputElementInterface::class, DiscountedProductsCheckingPeriodInputElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set(LowestPriceFlagElementInterface::class, LowestPriceFlagElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set(ExcludeTaxonsFromShowingLowestPriceInputElementInterface::class, ExcludeTaxonsFromShowingLowestPriceInputElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
        ->args([service(AutocompleteHelperInterface::class)])
    ;

    $services
        ->set(FormElementInterface::class, PromotionFormElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
        ->args([service(AutocompleteHelperInterface::class)])
    ;

    $services
        ->set('sylius.behat.element.admin.shipping_method.form', ShippingMethodFormElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set('sylius.behat.element.admin.product_option.form', ProductOptionFormElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
    ;

    $services
        ->set('sylius.behat.element.admin.customer.form', CustomerFormElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
        ->args([service('sylius.behat.shared_storage')])
    ;

    $services
        ->set('sylius.behat.element.admin.zone.form', ZoneFormElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
    ;

    $services
        ->set('sylius.behat.element.admin.taxon.form', TaxonFormElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
        ->args([service(AutocompleteHelperInterface::class)])
    ;

    $services
        ->set('sylius.behat.element.admin.taxon.image_form', ImageFormElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
    ;

    $services
        ->set('sylius.behat.element.admin.taxon.tree', TreeElement::class)
        ->parent('sylius.behat.element')
    ;

    $services
        ->set('sylius.behat.element.admin.promotion_coupon.form', PromotionCouponFormElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
    ;

    $services
        ->set('sylius.behat.element.admin.tax_category.form', TaxCategoryFormElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
    ;

    $services
        ->set('sylius.behat.element.admin.currency.form', CurrencyFormElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
        ->args([service(AutocompleteHelperInterface::class)])
    ;

    $services
        ->set('sylius.behat.element.admin.locale.form', LocaleFormElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
        ->args([service(AutocompleteHelperInterface::class)])
    ;

    $services
        ->set('sylius.behat.element.admin.exchange_rate.form', ExchangeRateFormElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
    ;

    $services
        ->set('sylius.behat.element.admin.customer_group.form', CustomerGroupFormElement::class)
        ->parent('sylius.behat.element.admin.crud.form')
    ;
};
