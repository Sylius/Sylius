<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.twig.extension.sylius_bundle_loaded_checker', 'Sylius\Bundle\CoreBundle\Twig\BundleLoadedCheckerExtension')
        ->args(['%kernel.bundles%'])
        ->tag('twig.extension');

    $services->set('sylius.twig.extension.price', 'Sylius\Bundle\CoreBundle\Twig\PriceExtension')
        ->private()
        ->args([service('sylius.calculator.product_variant_price')])
        ->tag('twig.extension');

    $services->set('sylius.twig.extension.variant_resolver', 'Sylius\Bundle\CoreBundle\Twig\VariantResolverExtension')
        ->private()
        ->args([service('sylius.resolver.product_variant')])
        ->tag('twig.extension');

    $services->set('sylius.twig.extension.channel_url', 'Sylius\Bundle\CoreBundle\Twig\ChannelUrlExtension')
        ->private()
        ->args([
            service('sylius.context.channel'),
            service('url_helper'),
            '%sylius.unsecured_urls%',
        ])
        ->tag('twig.extension');

    $services->set('sylius.twig.extension.product_translation', 'Sylius\Bundle\CoreBundle\Twig\ProductTranslationExtension')
        ->private()
        ->args([service('sylius.provider.channel_based_product_translation')])
        ->tag('twig.extension');
};
