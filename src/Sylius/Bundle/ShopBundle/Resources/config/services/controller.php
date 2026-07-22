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

use Sylius\Bundle\ShopBundle\Controller\CartCheckoutAction;
use Sylius\Bundle\ShopBundle\Controller\CartSummaryAction;
use Sylius\Bundle\ShopBundle\Controller\ChangePasswordAction;
use Sylius\Bundle\ShopBundle\Controller\ContactController;
use Sylius\Bundle\ShopBundle\Controller\CurrencySwitchController;
use Sylius\Bundle\ShopBundle\Controller\LocaleSwitchController;
use Sylius\Bundle\ShopBundle\Controller\OrderThankYouAction;
use Sylius\Bundle\ShopBundle\Controller\RegistrationThankYouController;
use Sylius\Bundle\ShopBundle\Controller\RequestPasswordResetTokenAction;
use Sylius\Bundle\ShopBundle\Controller\ResetPasswordAction;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius_shop.controller.cart_checkout', CartCheckoutAction::class)
        ->args([
            service('twig'),
            service('sylius.context.cart'),
            service('form.factory'),
            service('event_dispatcher'),
            service('router.default'),
            service('doctrine.orm.default_entity_manager'),
            service('sylius.resetter.cart_changes'),
            service('request_stack'),
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set('sylius_shop.controller.cart_summary', CartSummaryAction::class)
        ->args([
            service('twig'),
            service('sylius.context.cart'),
            service('sylius.repository.order'),
            service('form.factory'),
            service('event_dispatcher'),
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set('sylius_shop.controller.change_password', ChangePasswordAction::class)
        ->args([
            service('twig'),
            service('security.token_storage'),
            service('form.factory'),
            service('sylius.command_bus'),
            service('router.default'),
            service('request_stack')
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set('sylius_shop.controller.contact', ContactController::class)
        ->args([
            service('router'),
            service('form.factory'),
            service('twig'),
            service('sylius.context.channel'),
            service('sylius.context.customer'),
            service('sylius.context.locale'),
            service('sylius_shop.mailer.contact_email_manager'),
        ])
    ;

    $services
        ->set('sylius_shop.controller.currency_switch', CurrencySwitchController::class)
        ->args([
            service('sylius.storage.currency'),
            service('sylius.context.channel'),
            service('router'),
        ])
    ;

    $services
        ->set('sylius_shop.controller.locale_switch', LocaleSwitchController::class)
        ->args([
            service('sylius.provider.locale'),
            service('sylius_shop.locale_switcher'),
        ])
    ;

    $services
        ->set('sylius_shop.controller.order_thank_you', OrderThankYouAction::class)
        ->args([
            service('twig'),
            service('sylius.repository.order'),
            service('router.default'),
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set('sylius_shop.controller.register_thank_you', RegistrationThankYouController::class)
        ->args([
            service('twig'),
            service('sylius.context.channel'),
            service('router.default'),
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set('sylius_shop.controller.reset_password', ResetPasswordAction::class)
        ->args([
            service('twig'),
            service('router.default'),
            service('form.factory'),
            service('sylius.repository.shop_user'),
            service('translator'),
            service('request_stack'),
            service('sylius.command_dispatcher.reset_password.shop'),
            param('sylius.shop_user.token.password_reset.ttl'),
        ])
        ->tag('controller.service_arguments')
    ;

    $services
        ->set('sylius_shop.controller.request_reset_password_token', RequestPasswordResetTokenAction::class)
        ->args([
            service('twig'),
            service('router.default'),
            service('form.factory'),
            service('sylius.command_bus'),
            service('translator'),
            service('request_stack'),
        ])
        ->tag('controller.service_arguments')
    ;
};
