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

use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiUriBasedSectionResolver;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiUriBasedSectionResolver;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_api.section_resolver.admin_api_uri_based', AdminApiUriBasedSectionResolver::class)
        ->args(['%sylius.security.api_admin_route%'])
        ->tag('sylius.uri_based_section_resolver', ['priority' => 30]);

    $services->set('sylius_api.section_resolver.shop_api_uri_based', ShopApiUriBasedSectionResolver::class)
        ->args([
            '%sylius.security.api_shop_route%',
            'orders',
        ])
        ->tag('sylius.uri_based_section_resolver', ['priority' => 40]);
};
