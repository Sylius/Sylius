<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_api.taxon_search_filter.shop.product', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\TaxonFilter')
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

    $services->set('sylius_api.search_filter.shop.product_association_type.owner_based', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\OwnerBasedProductAssociationTypesFilter')
        ->args([
            service('sylius.section_resolver.uri_based'),
            '%sylius.model.product_association.class%',
            service('doctrine'),
        ])
        ->tag('api_platform.filter');

    $services->set('sylius_api.search_filter.shop.products_by_association', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\ProductByAssociationFilter')
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

    $services->set('sylius_api.name_with_locale_order_filter.shop.translatable', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\TranslationOrderNameAndLocaleFilter')
        ->args([service('doctrine')])
        ->tag('api_platform.filter');

    $services->set('sylius_api.option_value_search_filter.shop.product_variant', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\ProductVariantOptionValueFilter')
        ->args([
            service('api_platform.symfony.iri_converter'),
            service('doctrine'),
        ])
        ->tag('api_platform.filter');

    $services->set('sylius_api.product_code_search_filter.shop.product_option', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\ProductBasedProductOptionFilter')
        ->args([
            '%sylius.model.product.class%',
            service('doctrine'),
        ])
        ->tag('api_platform.filter');

    $services->set('sylius_api.product_code_search_filter.shop.product_option_value', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\ProductBasedProductOptionValueFilter')
        ->args([
            '%sylius.model.product.class%',
            service('doctrine'),
        ])
        ->tag('api_platform.filter');

    $services->set('sylius_api.price_order_filter.shop.product', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\ProductPriceOrderFilter')
        ->args([service('doctrine')])
        ->tag('api_platform.filter');
};
