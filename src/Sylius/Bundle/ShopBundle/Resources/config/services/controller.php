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

use Sylius\Bundle\ShopBundle\Controller\ContactController;
use Sylius\Bundle\ShopBundle\Controller\CurrencySwitchController;
use Sylius\Bundle\ShopBundle\Controller\LocaleSwitchController;
use Sylius\Bundle\ShopBundle\Controller\RegistrationThankYouController;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_shop.controller.contact', ContactController::class)
        ->args([
            service('router'),
            service('form.factory'),
            service('twig'),
            service('sylius.context.channel'),
            service('sylius.context.customer'),
            service('sylius.context.locale'),
            service('sylius_shop.mailer.contact_email_manager'),
        ]);

    $services->set('sylius_shop.controller.currency_switch', CurrencySwitchController::class)
        ->args([
            service('sylius.storage.currency'),
            service('sylius.context.channel'),
        ]);

    $services->set('sylius_shop.controller.locale_switch', LocaleSwitchController::class)
        ->args([
            service('sylius.provider.locale'),
            service('sylius_shop.locale_switcher'),
        ]);

    $services->set('sylius_shop.controller.register_thank_you', RegistrationThankYouController::class)
        ->args([
            service('twig'),
            service('sylius.context.channel'),
            service('router.default'),
        ])
        ->tag('controller.service_arguments');
};
