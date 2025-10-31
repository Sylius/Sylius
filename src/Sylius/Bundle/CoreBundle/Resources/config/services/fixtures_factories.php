<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->defaults()
        ->public();

    $services->set('sylius.fixture.example_factory.catalog_promotion', 'Sylius\Bundle\CoreBundle\Fixture\Factory\CatalogPromotionExampleFactory')
        ->args([
            service('sylius.factory.catalog_promotion'),
            service('sylius.repository.locale'),
            service('sylius.repository.channel'),
            service('sylius.fixture.example_factory.catalog_promotion_scope'),
            service('sylius.fixture.example_factory.catalog_promotion_action'),
        ]);

    $services->set('sylius.fixture.example_factory.catalog_promotion_scope', 'Sylius\Bundle\CoreBundle\Fixture\Factory\CatalogPromotionScopeExampleFactory')
        ->args([service('sylius.factory.catalog_promotion_scope')]);

    $services->set('sylius.fixture.example_factory.catalog_promotion_action', 'Sylius\Bundle\CoreBundle\Fixture\Factory\CatalogPromotionActionExampleFactory')
        ->args([service('sylius.factory.catalog_promotion_action')]);

    $services->set('sylius.fixture.example_factory.payment_method', 'Sylius\Bundle\CoreBundle\Fixture\Factory\PaymentMethodExampleFactory')
        ->args([
            service('sylius.factory.payment_method'),
            service('sylius.repository.locale'),
            service('sylius.repository.channel'),
        ]);

    $services->set('sylius.fixture.example_factory.shipping_category', 'Sylius\Bundle\CoreBundle\Fixture\Factory\ShippingCategoryExampleFactory')
        ->args([service('sylius.factory.shipping_category')]);

    $services->set('sylius.fixture.example_factory.shipping_method', 'Sylius\Bundle\CoreBundle\Fixture\Factory\ShippingMethodExampleFactory')
        ->args([
            service('sylius.factory.shipping_method'),
            service('sylius.repository.zone'),
            service('sylius.repository.shipping_category'),
            service('sylius.repository.locale'),
            service('sylius.repository.channel'),
            service('sylius.repository.tax_category'),
        ]);

    $services->set('sylius.fixture.example_factory.channel', 'Sylius\Bundle\CoreBundle\Fixture\Factory\ChannelExampleFactory')
        ->args([
            service('sylius.factory.channel'),
            service('sylius.repository.locale'),
            service('sylius.repository.currency'),
            service('sylius.repository.zone'),
            service('sylius.repository.taxon'),
            service('sylius.factory.shop_billing_data'),
        ]);

    $services->set('sylius.fixture.example_factory.customer_group', 'Sylius\Bundle\CoreBundle\Fixture\Factory\CustomerGroupExampleFactory')
        ->args([service('sylius.factory.customer_group')]);

    $services->set('sylius.fixture.example_factory.shop_user', 'Sylius\Bundle\CoreBundle\Fixture\Factory\ShopUserExampleFactory')
        ->args([
            service('sylius.factory.shop_user'),
            service('sylius.factory.customer'),
            service('sylius.repository.customer_group'),
        ]);

    $services->set('sylius.fixture.example_factory.admin_user', 'Sylius\Bundle\CoreBundle\Fixture\Factory\AdminUserExampleFactory')
        ->args([
            service('sylius.factory.admin_user'),
            '%locale%',
            service('file_locator'),
            service('sylius.uploader.image'),
            service('sylius.factory.avatar_image'),
        ]);

    $services->set('sylius.fixture.example_factory.promotion', 'Sylius\Bundle\CoreBundle\Fixture\Factory\PromotionExampleFactory')
        ->args([
            service('sylius.factory.promotion'),
            service('sylius.fixture.example_factory.promotion_rule'),
            service('sylius.fixture.example_factory.promotion_action'),
            service('sylius.repository.channel'),
            service('sylius.factory.promotion_coupon'),
            service('sylius.repository.locale'),
        ]);

    $services->set('sylius.fixture.example_factory.promotion_action', 'Sylius\Bundle\CoreBundle\Fixture\Factory\PromotionActionExampleFactory')
        ->args([service('sylius.factory.promotion_action')]);

    $services->set('sylius.fixture.example_factory.promotion_rule', 'Sylius\Bundle\CoreBundle\Fixture\Factory\PromotionRuleExampleFactory')
        ->args([service('sylius.factory.promotion_rule')]);

    $services->set('sylius.fixture.example_factory.product_association_type', 'Sylius\Bundle\CoreBundle\Fixture\Factory\ProductAssociationTypeExampleFactory')
        ->args([
            service('sylius.factory.product_association_type'),
            service('sylius.repository.locale'),
        ]);

    $services->set('sylius.fixture.example_factory.product_association', 'Sylius\Bundle\CoreBundle\Fixture\Factory\ProductAssociationExampleFactory')
        ->args([
            service('sylius.factory.product_association'),
            service('sylius.repository.product_association_type'),
            service('sylius.repository.product'),
        ]);

    $services->set('sylius.fixture.example_factory.product_option', 'Sylius\Bundle\CoreBundle\Fixture\Factory\ProductOptionExampleFactory')
        ->args([
            service('sylius.factory.product_option'),
            service('sylius.factory.product_option_value'),
            service('sylius.repository.locale'),
        ]);

    $services->set('sylius.fixture.example_factory.product_attribute', 'Sylius\Bundle\CoreBundle\Fixture\Factory\ProductAttributeExampleFactory')
        ->args([
            service('sylius.factory.product_attribute'),
            service('sylius.repository.locale'),
            '%sylius.attribute.attribute_types%',
        ]);

    $services->set('sylius.fixture.example_factory.product_review', 'Sylius\Bundle\CoreBundle\Fixture\Factory\ProductReviewExampleFactory')
        ->args([
            service('sylius.factory.product_review'),
            service('sylius.repository.product'),
            service('sylius.repository.customer'),
            service('sylius_abstraction.state_machine'),
        ]);

    $services->set('sylius.fixture.example_factory.tax_category', 'Sylius\Bundle\CoreBundle\Fixture\Factory\TaxCategoryExampleFactory')
        ->args([service('sylius.factory.tax_category')]);

    $services->set('sylius.fixture.example_factory.tax_rate', 'Sylius\Bundle\CoreBundle\Fixture\Factory\TaxRateExampleFactory')
        ->args([
            service('sylius.factory.tax_rate'),
            service('sylius.repository.zone'),
            service('sylius.repository.tax_category'),
        ]);

    $services->set('sylius.fixture.example_factory.taxon', 'Sylius\Bundle\CoreBundle\Fixture\Factory\TaxonExampleFactory')
        ->args([
            service('sylius.factory.taxon'),
            service('sylius.repository.taxon'),
            service('sylius.repository.locale'),
            service('sylius.generator.taxon_slug'),
        ]);

    $services->set('sylius.fixture.example_factory.product', 'Sylius\Bundle\CoreBundle\Fixture\Factory\ProductExampleFactory')
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

    $services->set('sylius.fixture.example_factory.address', 'Sylius\Bundle\CoreBundle\Fixture\Factory\AddressExampleFactory')
        ->args([
            service('sylius.factory.address'),
            service('sylius.repository.country'),
            service('sylius.repository.customer'),
        ]);

    $services->set('sylius.fixture.example_factory.order', 'Sylius\Bundle\CoreBundle\Fixture\Factory\OrderExampleFactory')
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
