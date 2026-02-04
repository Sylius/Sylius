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

use Sylius\Bundle\ShopBundle\Twig\Component\Common\CurrencySwitcherComponent;
use Sylius\Bundle\ShopBundle\Twig\Component\Common\LocaleSwitcherComponent;
use Sylius\Bundle\ShopBundle\Twig\Component\Common\TaxonMenuComponent;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\BySlugComponent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_shop.twig.component.locale_switcher', LocaleSwitcherComponent::class)
        ->args([
            service('sylius.context.locale'),
            service('sylius.provider.locale'),
        ])
        ->tag('sylius.twig_component', ['key' => 'sylius_shop:common:locale_switcher']);

    $services->set('sylius_shop.twig.component.currency_switcher', CurrencySwitcherComponent::class)
        ->args([
            service('sylius.context.channel'),
            service('sylius.context.currency'),
        ])
        ->tag('sylius.twig_component', ['key' => 'sylius_shop:common:currency_switcher']);

    $services->set('sylius_shop.twig.component.taxon_menu', TaxonMenuComponent::class)
        ->args([
            service('sylius.repository.taxon'),
            service('sylius.context.channel'),
            service('sylius.context.locale'),
        ])
        ->tag('sylius.twig_component', ['key' => 'sylius_shop:common:taxon_menu']);

    $services->set('sylius_shop.twig.component.product.by_slug', BySlugComponent::class)
        ->args([
            service('sylius.repository.product'),
            service('sylius.context.channel'),
            service('sylius.context.locale'),
        ])
        ->tag('sylius.twig_component', ['key' => 'sylius_shop:product.by_slug']);
};
