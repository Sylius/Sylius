<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->private();

    $services->set('sylius_api.open_api.factory', 'Sylius\Bundle\ApiBundle\OpenApi\Factory\OpenApiFactory')
        ->decorate('lexik_jwt_authentication.api_platform.openapi.factory')
        ->args([
            service('.inner'),
            tagged_iterator('sylius.open_api.modifier'),
        ]);

    $services->set('sylius_api.open_api.documentation_modifier.accept_language_header', 'Sylius\Bundle\ApiBundle\OpenApi\Documentation\AcceptLanguageHeaderDocumentationModifier')
        ->args([service('sylius.repository.locale')])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.administrator', 'Sylius\Bundle\ApiBundle\OpenApi\Documentation\AdministratorDocumentationModifier')
        ->args(['%sylius.security.api_route%'])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.attribute_type', 'Sylius\Bundle\ApiBundle\OpenApi\Documentation\AttributeTypeDocumentationModifier')
        ->args([service('sylius.registry.attribute_type')])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.product', 'Sylius\Bundle\ApiBundle\OpenApi\Documentation\ProductDocumentationModifier')
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.image', 'Sylius\Bundle\ApiBundle\OpenApi\Documentation\ImageDocumentationModifier')
        ->args([service('sylius_api.provider.liip_image_filters')])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.product_review', 'Sylius\Bundle\ApiBundle\OpenApi\Documentation\ProductReviewDocumentationModifier')
        ->args(['%sylius.security.api_route%'])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.product_slug', 'Sylius\Bundle\ApiBundle\OpenApi\Documentation\ProductSlugDocumentationModifier')
        ->args(['%sylius.security.api_route%'])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.product_variant', 'Sylius\Bundle\ApiBundle\OpenApi\Documentation\ProductVariantDocumentationModifier')
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.shipping_method', 'Sylius\Bundle\ApiBundle\OpenApi\Documentation\ShippingMethodDocumentationModifier')
        ->args([
            '%sylius.security.api_route%',
            '%sylius.shipping_method_rules%',
            '%sylius.shipping_calculators%',
        ])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.customer', 'Sylius\Bundle\ApiBundle\OpenApi\Documentation\CustomerDocumentationModifier')
        ->args(['%sylius.security.api_route%'])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.statistics', 'Sylius\Bundle\ApiBundle\OpenApi\Documentation\StatisticsDocumentationModifier')
        ->args([
            '%sylius.security.api_route%',
            service('clock'),
            '%sylius_core.orders_statistics.intervals_map%',
        ])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.promotion', 'Sylius\Bundle\ApiBundle\OpenApi\Documentation\PromotionDocumentationModifier')
        ->args([
            '%sylius.security.api_route%',
            '%sylius.promotion_actions%',
            '%sylius.promotion_rules%',
        ])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.not_prefixed_routes_removal', 'Sylius\Bundle\ApiBundle\OpenApi\Documentation\NotPrefixedRoutesRemovalDocumentationModifier')
        ->args([['' => '%sylius.security.api_route%']])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.order_adjustments', 'Sylius\Bundle\ApiBundle\OpenApi\Documentation\OrderAdjustmentsTypeDocumentationModifier')
        ->args([
            '%sylius.security.api_route%',
            '%sylius.model.adjustment.class%',
        ])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.address_log_entry', 'Sylius\Bundle\ApiBundle\OpenApi\Documentation\AddressLogEntryDocumentationModifier')
        ->tag('sylius.open_api.modifier');
};
