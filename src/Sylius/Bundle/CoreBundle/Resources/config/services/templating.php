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

use Sylius\Bundle\CoreBundle\Twig\BundleLoadedCheckerExtension;
use Sylius\Bundle\CoreBundle\Twig\ChannelUrlExtension;
use Sylius\Bundle\CoreBundle\Twig\CsrfProtectionEnabledExtension;
use Sylius\Bundle\CoreBundle\Twig\PriceExtension;
use Sylius\Bundle\CoreBundle\Twig\ProductTranslationExtension;
use Sylius\Bundle\CoreBundle\Twig\VariantResolverExtension;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.twig.extension.sylius_bundle_loaded_checker', BundleLoadedCheckerExtension::class)
        ->args(['%kernel.bundles%'])
        ->tag('twig.extension')
    ;

    $services
        ->set('sylius.twig.extension.price', PriceExtension::class)
        ->args([service('sylius.calculator.product_variant_price')])
        ->private()
        ->tag('twig.extension')
    ;

    $services
        ->set('sylius.twig.extension.variant_resolver', VariantResolverExtension::class)
        ->args([service('sylius.resolver.product_variant')])
        ->private()
        ->tag('twig.extension')
    ;

    $services
        ->set('sylius.twig.extension.channel_url', ChannelUrlExtension::class)
        ->args([
            service('sylius.context.channel'),
            service('url_helper'),
            '%sylius.unsecured_urls%',
        ])
        ->private()
        ->tag('twig.extension')
    ;

    $services
        ->set('sylius.twig.extension.product_translation', ProductTranslationExtension::class)
        ->args([service('sylius.provider.channel_based_product_translation')])
        ->private()
        ->tag('twig.extension')
    ;

    $services
        ->set('sylius.twig.extension.csrf_protection', CsrfProtectionEnabledExtension::class)
        ->args([service('service_container')])
        ->private()
        ->tag('twig.extension')
    ;
};
