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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\CoreBundle\Fixture\Factory\AddressExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AdminUserExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\CatalogPromotionActionExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\CatalogPromotionExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\CatalogPromotionScopeExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ChannelExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\CustomerGroupExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\OrderExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\PaymentMethodExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ProductAssociationExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ProductAssociationTypeExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ProductAttributeExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ProductExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ProductOptionExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ProductReviewExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\PromotionActionExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\PromotionExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\PromotionRuleExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ShippingCategoryExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ShippingMethodExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ShopUserExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\TaxCategoryExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\TaxRateExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\TaxonExampleFactory;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius.fixture.example_factory.catalog_promotion', CatalogPromotionExampleFactory::class)
        ->args([
            service('sylius.factory.catalog_promotion'),
            service('sylius.repository.locale'),
            service('sylius.repository.channel'),
            service('sylius.fixture.example_factory.catalog_promotion_scope'),
            service('sylius.fixture.example_factory.catalog_promotion_action'),
        ]);

    $services->set('sylius.fixture.example_factory.catalog_promotion_scope', CatalogPromotionScopeExampleFactory::class)
        ->args([service('sylius.factory.catalog_promotion_scope')]);

    $services->set('sylius.fixture.example_factory.catalog_promotion_action', CatalogPromotionActionExampleFactory::class)
        ->args([service('sylius.factory.catalog_promotion_action')]);

    $services->set('sylius.fixture.example_factory.payment_method', PaymentMethodExampleFactory::class)
        ->args([
            service('sylius.factory.payment_method'),
            service('sylius.repository.locale'),
            service('sylius.repository.channel'),
        ]);

    $services->set('sylius.fixture.example_factory.shipping_category', ShippingCategoryExampleFactory::class)
        ->args([service('sylius.factory.shipping_category')]);

    $services->set('sylius.fixture.example_factory.shipping_method', ShippingMethodExampleFactory::class)
        ->args([
            service('sylius.factory.shipping_method'),
            service('sylius.repository.zone'),
            service('sylius.repository.shipping_category'),
            service('sylius.repository.locale'),
            service('sylius.repository.channel'),
            service('sylius.repository.tax_category'),
        ]);

    $services->set('sylius.fixture.example_factory.channel', ChannelExampleFactory::class)
        ->args([
            service('sylius.factory.channel'),
            service('sylius.repository.locale'),
            service('sylius.repository.currency'),
            service('sylius.repository.zone'),
            service('sylius.repository.taxon'),
            service('sylius.factory.shop_billing_data'),
        ]);

    $services->set('sylius.fixture.example_factory.customer_group', CustomerGroupExampleFactory::class)
        ->args([service('sylius.factory.customer_group')]);

    $services->set('sylius.fixture.example_factory.shop_user', ShopUserExampleFactory::class)
        ->args([
            service('sylius.factory.shop_user'),
            service('sylius.factory.customer'),
            service('sylius.repository.customer_group'),
        ]);

    $services->set('sylius.fixture.example_factory.admin_user', AdminUserExampleFactory::class)
        ->args([
            service('sylius.factory.admin_user'),
            '%locale%',
            service('file_locator'),
            service('sylius.uploader.image'),
            service('sylius.factory.avatar_image'),
        ]);

    $services->set('sylius.fixture.example_factory.promotion', PromotionExampleFactory::class)
        ->args([
            service('sylius.factory.promotion'),
            service('sylius.fixture.example_factory.promotion_rule'),
            service('sylius.fixture.example_factory.promotion_action'),
            service('sylius.repository.channel'),
            service('sylius.factory.promotion_coupon'),
            service('sylius.repository.locale'),
        ]);

    $services->set('sylius.fixture.example_factory.promotion_action', PromotionActionExampleFactory::class)
        ->args([service('sylius.factory.promotion_action')]);

    $services->set('sylius.fixture.example_factory.promotion_rule', PromotionRuleExampleFactory::class)
        ->args([service('sylius.factory.promotion_rule')]);

    $services->set('sylius.fixture.example_factory.product_association_type', ProductAssociationTypeExampleFactory::class)
        ->args([
            service('sylius.factory.product_association_type'),
            service('sylius.repository.locale'),
        ]);

    $services->set('sylius.fixture.example_factory.product_association', ProductAssociationExampleFactory::class)
        ->args([
            service('sylius.factory.product_association'),
            service('sylius.repository.product_association_type'),
            service('sylius.repository.product'),
        ]);

    $services->set('sylius.fixture.example_factory.product_option', ProductOptionExampleFactory::class)
        ->args([
            service('sylius.factory.product_option'),
            service('sylius.factory.product_option_value'),
            service('sylius.repository.locale'),
        ]);

    $services->set('sylius.fixture.example_factory.product_attribute', ProductAttributeExampleFactory::class)
        ->args([
            service('sylius.factory.product_attribute'),
            service('sylius.repository.locale'),
            '%sylius.attribute.attribute_types%',
        ]);

    $services->set('sylius.fixture.example_factory.product_review', ProductReviewExampleFactory::class)
        ->args([
            service('sylius.factory.product_review'),
            service('sylius.repository.product'),
            service('sylius.repository.customer'),
            service('sylius_abstraction.state_machine'),
        ]);

    $services->set('sylius.fixture.example_factory.tax_category', TaxCategoryExampleFactory::class)
        ->args([service('sylius.factory.tax_category')]);

    $services->set('sylius.fixture.example_factory.tax_rate', TaxRateExampleFactory::class)
        ->args([
            service('sylius.factory.tax_rate'),
            service('sylius.repository.zone'),
            service('sylius.repository.tax_category'),
        ]);

    $services->set('sylius.fixture.example_factory.taxon', TaxonExampleFactory::class)
        ->args([
            service('sylius.factory.taxon'),
            service('sylius.repository.taxon'),
            service('sylius.repository.locale'),
            service('sylius.generator.taxon_slug'),
        ]);

    $services->set('sylius.fixture.example_factory.product', ProductExampleFactory::class)
        ->args([
            service('sylius.factory.product'),
            service('sylius.factory.product_variant'),
            service('sylius.factory.channel_pricing'),
            service('sylius.generator.product_variant'),
            service('sylius.factory.product_attribute_value'),
            service('sylius.factory.product_image'),
            service('sylius.factory.product_taxon'),
            service('sylius.uploader.image'),
            service('sylius.generator.slug'),
            service('sylius.repository.taxon'),
            service('sylius.repository.product_attribute'),
            service('sylius.repository.product_option'),
            service('sylius.repository.channel'),
            service('sylius.repository.locale'),
            service('sylius.repository.tax_category'),
            service('file_locator'),
        ]);

    $services->set('sylius.fixture.example_factory.address', AddressExampleFactory::class)
        ->args([
            service('sylius.factory.address'),
            service('sylius.repository.country'),
            service('sylius.repository.customer'),
        ]);

    $services->set('sylius.fixture.example_factory.order', OrderExampleFactory::class)
        ->args([
            service('sylius.factory.order'),
            service('sylius.factory.order_item'),
            service('sylius.modifier.order_item_quantity'),
            service('sylius.manager.order'),
            service('sylius.repository.channel'),
            service('sylius.repository.customer'),
            service('sylius.repository.product'),
            service('sylius.repository.country'),
            service('sylius.repository.payment_method'),
            service('sylius.repository.shipping_method'),
            service('sylius.factory.address'),
            service('sylius_abstraction.state_machine'),
            service('sylius.checker.order_shipping_method_selection_requirement'),
            service('sylius.checker.order_payment_method_selection_requirement'),
        ]);
};
