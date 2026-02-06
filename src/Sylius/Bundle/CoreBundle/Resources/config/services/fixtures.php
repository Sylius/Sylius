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

use Sylius\Bundle\CoreBundle\Fixture\AddressFixture;
use Sylius\Bundle\CoreBundle\Fixture\AdminUserFixture;
use Sylius\Bundle\CoreBundle\Fixture\CatalogPromotionFixture;
use Sylius\Bundle\CoreBundle\Fixture\ChannelFixture;
use Sylius\Bundle\CoreBundle\Fixture\CurrencyFixture;
use Sylius\Bundle\CoreBundle\Fixture\CustomerGroupFixture;
use Sylius\Bundle\CoreBundle\Fixture\GeographicalFixture;
use Sylius\Bundle\CoreBundle\Fixture\LocaleFixture;
use Sylius\Bundle\CoreBundle\Fixture\OrderFixture;
use Sylius\Bundle\CoreBundle\Fixture\PaymentFixture;
use Sylius\Bundle\CoreBundle\Fixture\PaymentMethodFixture;
use Sylius\Bundle\CoreBundle\Fixture\ProductAssociationFixture;
use Sylius\Bundle\CoreBundle\Fixture\ProductAssociationTypeFixture;
use Sylius\Bundle\CoreBundle\Fixture\ProductAttributeFixture;
use Sylius\Bundle\CoreBundle\Fixture\ProductFixture;
use Sylius\Bundle\CoreBundle\Fixture\ProductOptionFixture;
use Sylius\Bundle\CoreBundle\Fixture\ProductReviewFixture;
use Sylius\Bundle\CoreBundle\Fixture\PromotionFixture;
use Sylius\Bundle\CoreBundle\Fixture\ShippingCategoryFixture;
use Sylius\Bundle\CoreBundle\Fixture\ShippingMethodFixture;
use Sylius\Bundle\CoreBundle\Fixture\ShopUserFixture;
use Sylius\Bundle\CoreBundle\Fixture\SimilarProductAssociationFixture;
use Sylius\Bundle\CoreBundle\Fixture\TaxCategoryFixture;
use Sylius\Bundle\CoreBundle\Fixture\TaxRateFixture;
use Sylius\Bundle\CoreBundle\Fixture\TaxonFixture;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.fixture.locale', LocaleFixture::class)
        ->args([
            service('sylius.factory.locale'),
            service('sylius.manager.locale'),
            '%locale%',
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.currency', CurrencyFixture::class)
        ->args([
            service('sylius.factory.currency'),
            service('sylius.manager.currency'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.geographical', GeographicalFixture::class)
        ->args([
            service('sylius.factory.country'),
            service('sylius.manager.country'),
            service('sylius.factory.province'),
            service('sylius.manager.province'),
            service('sylius.factory.zone'),
            service('sylius.manager.zone'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.payment_method', PaymentMethodFixture::class)
        ->args([
            service('sylius.manager.payment_method'),
            service('sylius.fixture.example_factory.payment_method'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.shipping_category', ShippingCategoryFixture::class)
        ->args([
            service('sylius.manager.shipping_category'),
            service('sylius.fixture.example_factory.shipping_category'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.shipping_method', ShippingMethodFixture::class)
        ->args([
            service('sylius.manager.shipping_method'),
            service('sylius.fixture.example_factory.shipping_method'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.catalog_promotion', CatalogPromotionFixture::class)
        ->args([
            service('sylius.manager.catalog_promotion'),
            service('sylius.fixture.example_factory.catalog_promotion'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.channel', ChannelFixture::class)
        ->args([
            service('sylius.manager.channel'),
            service('sylius.fixture.example_factory.channel'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.customer_group', CustomerGroupFixture::class)
        ->args([
            service('sylius.manager.customer_group'),
            service('sylius.fixture.example_factory.customer_group'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.shop_user', ShopUserFixture::class)
        ->args([
            service('sylius.manager.shop_user'),
            service('sylius.fixture.example_factory.shop_user'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.admin_user', AdminUserFixture::class)
        ->args([
            service('sylius.manager.admin_user'),
            service('sylius.fixture.example_factory.admin_user'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.product_association_type', ProductAssociationTypeFixture::class)
        ->args([
            service('sylius.manager.product_association_type'),
            service('sylius.fixture.example_factory.product_association_type'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.product_association', ProductAssociationFixture::class)
        ->args([
            service('sylius.manager.product_association'),
            service('sylius.fixture.example_factory.product_association'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.similar_product_association', SimilarProductAssociationFixture::class)
        ->args([
            service('sylius.fixture.product_association_type'),
            service('sylius.fixture.product_association'),
            service('sylius.repository.product'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.product_option', ProductOptionFixture::class)
        ->args([
            service('sylius.manager.product_option'),
            service('sylius.fixture.example_factory.product_option'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.product_attribute', ProductAttributeFixture::class)
        ->args([
            service('sylius.manager.product_attribute'),
            service('sylius.fixture.example_factory.product_attribute'),
            '%sylius.attribute.attribute_types%',
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.product_review', ProductReviewFixture::class)
        ->args([
            service('sylius.manager.product_review'),
            service('sylius.fixture.example_factory.product_review'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.tax_category', TaxCategoryFixture::class)
        ->args([
            service('sylius.manager.tax_category'),
            service('sylius.fixture.example_factory.tax_category'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.tax_rate', TaxRateFixture::class)
        ->args([
            service('sylius.manager.tax_rate'),
            service('sylius.fixture.example_factory.tax_rate'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.promotion', PromotionFixture::class)
        ->args([
            service('sylius.manager.promotion'),
            service('sylius.fixture.example_factory.promotion'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.taxon', TaxonFixture::class)
        ->args([
            service('sylius.manager.taxon'),
            service('sylius.fixture.example_factory.taxon'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.product', ProductFixture::class)
        ->args([
            service('sylius.manager.product'),
            service('sylius.fixture.example_factory.product'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.order', OrderFixture::class)
        ->args([
            service('sylius.manager.order'),
            service('sylius.fixture.example_factory.order'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.address', AddressFixture::class)
        ->args([
            service('sylius.manager.address'),
            service('sylius.fixture.example_factory.address'),
        ])
        ->tag('sylius_fixtures.fixture');

    $services->set('sylius.fixture.payment', PaymentFixture::class)
        ->args([
            service('sylius.repository.payment'),
            service('sylius_abstraction.state_machine'),
            service('sylius.manager.payment'),
        ])
        ->tag('sylius_fixtures.fixture');
};
