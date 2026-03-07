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

use Sylius\Bundle\AdminBundle\Action\Account\RenderRequestPasswordResetPageAction;
use Sylius\Bundle\AdminBundle\Action\Account\RenderResetPasswordPageAction;
use Sylius\Bundle\AdminBundle\Action\Account\RequestPasswordResetAction;
use Sylius\Bundle\AdminBundle\Action\Account\ResetPasswordAction;
use Sylius\Bundle\AdminBundle\Action\RemoveAvatarAction;
use Sylius\Bundle\AdminBundle\Action\ResendOrderConfirmationEmailAction;
use Sylius\Bundle\AdminBundle\Action\ResendShipmentConfirmationEmailAction;
use Sylius\Bundle\AdminBundle\Controller\CustomerStatisticsController;
use Sylius\Bundle\AdminBundle\Controller\DashboardController;
use Sylius\Bundle\AdminBundle\Controller\GeneratePromotionCouponsAction;
use Sylius\Bundle\AdminBundle\Controller\RedirectHandler;
use Sylius\Bundle\AdminBundle\Controller\RemoveCatalogPromotionAction;
use Sylius\Bundle\AdminBundle\Controller\UpdateProductTaxonPositionAction;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.admin.notification.uri', 'https://gus.sylius.com/version');

    $services->defaults()->public();

    $services
        ->set('sylius_admin.controller.account.render_reset_password_page', RenderResetPasswordPageAction::class)
        ->args([
            service('sylius.repository.admin_user'),
            service('form.factory'),
            service('request_stack'),
            service('router'),
            service('twig'),
            '%sylius.admin_user.token.password_reset.ttl%',
        ])
    ;

    $services
        ->set('sylius_admin.controller.account.reset_password', ResetPasswordAction::class)
        ->args([
            service('form.factory'),
            service('sylius.command_dispatcher.reset_password'),
            service('request_stack'),
            service('router'),
            service('twig'),
        ])
    ;

    $services
        ->set('sylius_admin.controller.remove_avatar', RemoveAvatarAction::class)
        ->args([
            service('sylius.repository.avatar_image'),
            service('router'),
            service('security.csrf.token_manager'),
        ])
    ;

    $services
        ->set('sylius_admin.controller.resend_order_confirmation_email', ResendOrderConfirmationEmailAction::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius.command_dispatcher.resend_order_confirmation_email'),
            service('security.csrf.token_manager'),
            service('request_stack'),
            service('router'),
        ])
    ;

    $services
        ->set('sylius_admin.controller.resend_shipment_confirmation_email', ResendShipmentConfirmationEmailAction::class)
        ->args([
            service('sylius.repository.shipment'),
            service('sylius.command_dispatcher.resend_shipment_confirmation_email'),
            service('security.csrf.token_manager'),
            service('request_stack'),
        ])
    ;

    $services
        ->set('sylius_admin.controller.update_product_taxon_positions', UpdateProductTaxonPositionAction::class)
        ->args([
            service('sylius.repository.product_taxon'),
            service('request_stack'),
            service('sylius.positioner'),
            service('doctrine.orm.default_entity_manager'),
            service('security.csrf.token_manager'),
        ])
    ;

    $services
        ->set('sylius_admin.controller.account.render_request_password_reset_page', RenderRequestPasswordResetPageAction::class)
        ->args([
            service('twig'),
            service('form.factory'),
        ])
    ;

    $services
        ->set('sylius_admin.controller.account.request_password_reset', RequestPasswordResetAction::class)
        ->args([
            service('form.factory'),
            service('sylius.command_bus'),
            service('request_stack'),
            service('router'),
            service('twig'),
        ])
    ;

    $services
        ->set('sylius_admin.controller.dashboard', DashboardController::class)
        ->args([
            service('sylius.repository.channel'),
            service('twig'),
            service('router'),
        ])
    ;

    $services
        ->set('sylius_admin.controller.customer_statistics', CustomerStatisticsController::class)
        ->args([
            service('sylius.provider.statistics.customer'),
            service('sylius.repository.customer'),
            service('twig'),
        ])
    ;

    $services
        ->set('sylius_admin.controller.remove_catalog_promotion', RemoveCatalogPromotionAction::class)
        ->args([service('sylius.processor.catalog_promotion.removal')])
    ;

    $services
        ->set('sylius_admin.resource_controller.redirect_handler', RedirectHandler::class)
        ->decorate('sylius.resource_controller.redirect_handler')
        ->args([
            service('.inner'),
            service('sylius.grid.filter_storage'),
        ])
        ->private()
    ;
};
