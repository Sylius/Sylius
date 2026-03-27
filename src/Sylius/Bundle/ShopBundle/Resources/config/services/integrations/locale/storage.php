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

use Sylius\Bundle\ShopBundle\Locale\StorageBasedLocaleSwitcher;
use Sylius\Bundle\ShopBundle\Router\LocaleStrippingRouter;
use Sylius\Component\Core\Locale\Context\StorageBasedLocaleContext;
use Sylius\Component\Core\Locale\LocaleStorage;
use Sylius\Component\Core\Locale\LocaleStorageInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_shop.locale_switcher', StorageBasedLocaleSwitcher::class)
        ->args([
            service('sylius_shop.storage.locale'),
            service('sylius.context.channel'),
            service('router'),
        ])
    ;

    $services->set('sylius_shop.storage.locale', LocaleStorage::class)->args([service('sylius.storage.session')]);
    $services->alias(LocaleStorageInterface::class, 'sylius_shop.storage.locale');

    $services
        ->set('sylius_shop.context.locale.storage_based', StorageBasedLocaleContext::class)
        ->args([
            service('sylius.context.channel'),
            service('sylius_shop.storage.locale'),
            service('sylius.provider.locale'),
        ])
        ->tag('sylius.context.locale', ['priority' => 64])
    ;

    $services
        ->set('sylius_shop.router.locale_stripping', LocaleStrippingRouter::class)
        ->decorate('router', null, 1024)
        ->args([
            service('.inner'),
            service('sylius.context.locale'),
        ])
    ;
};
