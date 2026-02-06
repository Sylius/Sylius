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

use Sylius\Bundle\ApiBundle\OpenApi\Documentation\AcceptLanguageHeaderDocumentationModifier;
use Sylius\Bundle\ApiBundle\OpenApi\Documentation\AddressLogEntryDocumentationModifier;
use Sylius\Bundle\ApiBundle\OpenApi\Documentation\AdministratorDocumentationModifier;
use Sylius\Bundle\ApiBundle\OpenApi\Documentation\AttributeTypeDocumentationModifier;
use Sylius\Bundle\ApiBundle\OpenApi\Documentation\CustomerDocumentationModifier;
use Sylius\Bundle\ApiBundle\OpenApi\Documentation\ImageDocumentationModifier;
use Sylius\Bundle\ApiBundle\OpenApi\Documentation\NotPrefixedRoutesRemovalDocumentationModifier;
use Sylius\Bundle\ApiBundle\OpenApi\Documentation\OrderAdjustmentsTypeDocumentationModifier;
use Sylius\Bundle\ApiBundle\OpenApi\Documentation\ProductDocumentationModifier;
use Sylius\Bundle\ApiBundle\OpenApi\Documentation\ProductReviewDocumentationModifier;
use Sylius\Bundle\ApiBundle\OpenApi\Documentation\ProductSlugDocumentationModifier;
use Sylius\Bundle\ApiBundle\OpenApi\Documentation\ProductVariantDocumentationModifier;
use Sylius\Bundle\ApiBundle\OpenApi\Documentation\PromotionDocumentationModifier;
use Sylius\Bundle\ApiBundle\OpenApi\Documentation\ShippingMethodDocumentationModifier;
use Sylius\Bundle\ApiBundle\OpenApi\Documentation\StatisticsDocumentationModifier;
use Sylius\Bundle\ApiBundle\OpenApi\Factory\OpenApiFactory;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->private();

    $services->set('sylius_api.open_api.factory', OpenApiFactory::class)
        ->decorate('lexik_jwt_authentication.api_platform.openapi.factory')
        ->args([
            service('.inner'),
            tagged_iterator('sylius.open_api.modifier'),
        ]);

    $services->set('sylius_api.open_api.documentation_modifier.accept_language_header', AcceptLanguageHeaderDocumentationModifier::class)
        ->args([service('sylius.repository.locale')])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.administrator', AdministratorDocumentationModifier::class)
        ->args(['%sylius.security.api_route%'])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.attribute_type', AttributeTypeDocumentationModifier::class)
        ->args([service('sylius.registry.attribute_type')])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.product', ProductDocumentationModifier::class)
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.image', ImageDocumentationModifier::class)
        ->args([service('sylius_api.provider.liip_image_filters')])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.product_review', ProductReviewDocumentationModifier::class)
        ->args(['%sylius.security.api_route%'])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.product_slug', ProductSlugDocumentationModifier::class)
        ->args(['%sylius.security.api_route%'])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.product_variant', ProductVariantDocumentationModifier::class)
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.shipping_method', ShippingMethodDocumentationModifier::class)
        ->args([
            '%sylius.security.api_route%',
            '%sylius.shipping_method_rules%',
            '%sylius.shipping_calculators%',
        ])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.customer', CustomerDocumentationModifier::class)
        ->args(['%sylius.security.api_route%'])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.statistics', StatisticsDocumentationModifier::class)
        ->args([
            '%sylius.security.api_route%',
            service('clock'),
            '%sylius_core.orders_statistics.intervals_map%',
        ])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.promotion', PromotionDocumentationModifier::class)
        ->args([
            '%sylius.security.api_route%',
            '%sylius.promotion_actions%',
            '%sylius.promotion_rules%',
        ])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.not_prefixed_routes_removal', NotPrefixedRoutesRemovalDocumentationModifier::class)
        ->args([['' => '%sylius.security.api_route%']])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.order_adjustments', OrderAdjustmentsTypeDocumentationModifier::class)
        ->args([
            '%sylius.security.api_route%',
            '%sylius.model.adjustment.class%',
        ])
        ->tag('sylius.open_api.modifier');

    $services->set('sylius_api.open_api.documentation_modifier.address_log_entry', AddressLogEntryDocumentationModifier::class)
        ->tag('sylius.open_api.modifier');
};
