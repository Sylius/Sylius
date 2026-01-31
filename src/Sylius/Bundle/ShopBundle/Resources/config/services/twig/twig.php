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

use Sylius\Bundle\ShopBundle\Twig\ErrorTemplateFinder\ErrorTemplateFinder;
use Sylius\Bundle\ShopBundle\Twig\OrderItemOriginalPriceToDisplayExtension;
use Sylius\Bundle\ShopBundle\Twig\OrderPaymentsExtension;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_shop.twig.error_template_finder', ErrorTemplateFinder::class)
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('twig'),
        ])
        ->tag('sylius.twig.error_template_finder');

    $services->set('sylius_shop.twig.extension.order_item_original_price_to_display', OrderItemOriginalPriceToDisplayExtension::class)
        ->tag('twig.extension');

    $services->set('sylius_shop.twig.extension.order_payments', OrderPaymentsExtension::class)
        ->args([service('sylius.resolver.payment_methods')])
        ->tag('twig.extension');
};
