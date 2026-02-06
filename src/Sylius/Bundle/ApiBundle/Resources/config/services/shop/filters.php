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

use Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\OwnerBasedProductAssociationTypesFilter;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\ProductBasedProductOptionFilter;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\ProductBasedProductOptionValueFilter;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\ProductByAssociationFilter;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\ProductPriceOrderFilter;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\ProductVariantOptionValueFilter;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\TaxonFilter;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\TranslationOrderNameAndLocaleFilter;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_api.taxon_search_filter.shop.product', TaxonFilter::class)
        ->args([
            service('doctrine'),
            service('api_platform.symfony.iri_converter'),
        ])
        ->tag('api_platform.filter');

    $services->set('sylius_api.search_filter.shop.product')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['translations.name' => 'partial', 'productTaxons.taxon.code' => 'exact']])
        ->tag('api_platform.filter');

    $services->set('sylius_api.order_filter.shop.product')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->args([['code' => '', 'createdAt' => '']])
        ->tag('api_platform.filter');

    $services->set('sylius_api.search_filter.shop.image')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['type' => 'exact']])
        ->tag('api_platform.filter');

    $services->set('sylius_api.search_filter.shop.product_association_type.owner_based', OwnerBasedProductAssociationTypesFilter::class)
        ->args([
            service('sylius.section_resolver.uri_based'),
            '%sylius.model.product_association.class%',
            service('doctrine'),
        ])
        ->tag('api_platform.filter');

    $services->set('sylius_api.search_filter.shop.products_by_association', ProductByAssociationFilter::class)
        ->args([
            service('sylius.section_resolver.uri_based'),
            '%sylius.model.product_association.class%',
            service('doctrine'),
        ])
        ->tag('api_platform.filter');

    $services->set('sylius_api.search_filter.shop.product_variant')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['product' => 'exact']])
        ->tag('api_platform.filter');

    $services->set('sylius_api.order_filter.shop.product_review')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->args([['createdAt' => '']])
        ->tag('api_platform.filter');

    $services->set('sylius_api.search_filter.shop.product_review')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['reviewSubject' => 'exact']])
        ->tag('api_platform.filter');

    $services->set('sylius_api.name_with_locale_order_filter.shop.translatable', TranslationOrderNameAndLocaleFilter::class)
        ->args([service('doctrine')])
        ->tag('api_platform.filter');

    $services->set('sylius_api.option_value_search_filter.shop.product_variant', ProductVariantOptionValueFilter::class)
        ->args([
            service('api_platform.symfony.iri_converter'),
            service('doctrine'),
        ])
        ->tag('api_platform.filter');

    $services->set('sylius_api.product_code_search_filter.shop.product_option', ProductBasedProductOptionFilter::class)
        ->args([
            '%sylius.model.product.class%',
            service('doctrine'),
        ])
        ->tag('api_platform.filter');

    $services->set('sylius_api.product_code_search_filter.shop.product_option_value', ProductBasedProductOptionValueFilter::class)
        ->args([
            '%sylius.model.product.class%',
            service('doctrine'),
        ])
        ->tag('api_platform.filter');

    $services->set('sylius_api.price_order_filter.shop.product', ProductPriceOrderFilter::class)
        ->args([service('doctrine')])
        ->tag('api_platform.filter');
};
