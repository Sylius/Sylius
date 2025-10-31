<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.fixture.locale', 'Sylius\Bundle\CoreBundle\Fixture\LocaleFixture')
        ->args([
            service('sylius.factory.locale'),
            service('sylius.manager.locale'),
            '%locale%',
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.currency', 'Sylius\Bundle\CoreBundle\Fixture\CurrencyFixture')
        ->args([
            service('sylius.factory.currency'),
            service('sylius.manager.currency'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.geographical', 'Sylius\Bundle\CoreBundle\Fixture\GeographicalFixture')
        ->args([
            service('sylius.factory.country'),
            service('sylius.manager.country'),
            service('sylius.factory.province'),
            service('sylius.manager.province'),
            service('sylius.factory.zone'),
            service('sylius.manager.zone'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.payment_method', 'Sylius\Bundle\CoreBundle\Fixture\PaymentMethodFixture')
        ->args([
            service('sylius.manager.payment_method'),
            service('sylius.fixture.example_factory.payment_method'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.shipping_category', 'Sylius\Bundle\CoreBundle\Fixture\ShippingCategoryFixture')
        ->args([
            service('sylius.manager.shipping_category'),
            service('sylius.fixture.example_factory.shipping_category'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.shipping_method', 'Sylius\Bundle\CoreBundle\Fixture\ShippingMethodFixture')
        ->args([
            service('sylius.manager.shipping_method'),
            service('sylius.fixture.example_factory.shipping_method'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.catalog_promotion', 'Sylius\Bundle\CoreBundle\Fixture\CatalogPromotionFixture')
        ->args([
            service('sylius.manager.catalog_promotion'),
            service('sylius.fixture.example_factory.catalog_promotion'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.channel', 'Sylius\Bundle\CoreBundle\Fixture\ChannelFixture')
        ->args([
            service('sylius.manager.channel'),
            service('sylius.fixture.example_factory.channel'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.customer_group', 'Sylius\Bundle\CoreBundle\Fixture\CustomerGroupFixture')
        ->args([
            service('sylius.manager.customer_group'),
            service('sylius.fixture.example_factory.customer_group'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.shop_user', 'Sylius\Bundle\CoreBundle\Fixture\ShopUserFixture')
        ->args([
            service('sylius.manager.shop_user'),
            service('sylius.fixture.example_factory.shop_user'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.admin_user', 'Sylius\Bundle\CoreBundle\Fixture\AdminUserFixture')
        ->args([
            service('sylius.manager.admin_user'),
            service('sylius.fixture.example_factory.admin_user'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.product_association_type', 'Sylius\Bundle\CoreBundle\Fixture\ProductAssociationTypeFixture')
        ->args([
            service('sylius.manager.product_association_type'),
            service('sylius.fixture.example_factory.product_association_type'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.product_association', 'Sylius\Bundle\CoreBundle\Fixture\ProductAssociationFixture')
        ->args([
            service('sylius.manager.product_association'),
            service('sylius.fixture.example_factory.product_association'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.similar_product_association', 'Sylius\Bundle\CoreBundle\Fixture\SimilarProductAssociationFixture')
        ->args([
            service('sylius.fixture.product_association_type'),
            service('sylius.fixture.product_association'),
            service('sylius.repository.product'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.product_option', 'Sylius\Bundle\CoreBundle\Fixture\ProductOptionFixture')
        ->args([
            service('sylius.manager.product_option'),
            service('sylius.fixture.example_factory.product_option'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.product_attribute', 'Sylius\Bundle\CoreBundle\Fixture\ProductAttributeFixture')
        ->args([
            service('sylius.manager.product_attribute'),
            service('sylius.fixture.example_factory.product_attribute'),
            '%sylius.attribute.attribute_types%',
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.product_review', 'Sylius\Bundle\CoreBundle\Fixture\ProductReviewFixture')
        ->args([
            service('sylius.manager.product_review'),
            service('sylius.fixture.example_factory.product_review'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.tax_category', 'Sylius\Bundle\CoreBundle\Fixture\TaxCategoryFixture')
        ->args([
            service('sylius.manager.tax_category'),
            service('sylius.fixture.example_factory.tax_category'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.tax_rate', 'Sylius\Bundle\CoreBundle\Fixture\TaxRateFixture')
        ->args([
            service('sylius.manager.tax_rate'),
            service('sylius.fixture.example_factory.tax_rate'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.promotion', 'Sylius\Bundle\CoreBundle\Fixture\PromotionFixture')
        ->args([
            service('sylius.manager.promotion'),
            service('sylius.fixture.example_factory.promotion'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.taxon', 'Sylius\Bundle\CoreBundle\Fixture\TaxonFixture')
        ->args([
            service('sylius.manager.taxon'),
            service('sylius.fixture.example_factory.taxon'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.product', 'Sylius\Bundle\CoreBundle\Fixture\ProductFixture')
        ->args([
            service('sylius.manager.product'),
            service('sylius.fixture.example_factory.product'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.order', 'Sylius\Bundle\CoreBundle\Fixture\OrderFixture')
        ->args([
            service('sylius.manager.order'),
            service('sylius.fixture.example_factory.order'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.address', 'Sylius\Bundle\CoreBundle\Fixture\AddressFixture')
        ->args([
            service('sylius.manager.address'),
            service('sylius.fixture.example_factory.address'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.payment', 'Sylius\Bundle\CoreBundle\Fixture\PaymentFixture')
        ->args([
            service('sylius.repository.payment'),
            service('sylius_abstraction.state_machine'),
            service('sylius.manager.payment'),
        ])
        ->tag('sylius_fixtures.fixture');
};
