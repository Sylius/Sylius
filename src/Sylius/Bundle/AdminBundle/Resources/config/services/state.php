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
use Sylius\Bundle\AdminBundle\State\Processor\GeneratePromotionCouponsProcessor;
use Sylius\Bundle\AdminBundle\State\Provider\GeneratePromotionCouponsProvider;
use Sylius\Bundle\AdminBundle\Twig\Context\Factory\PromotionTwigContextFactory;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.admin.state_processor.generate_promotion_coupons', GeneratePromotionCouponsProcessor::class)
        ->args([
            service('sylius.generator.promotion_coupon'),
        ])
        ->tag('sylius.state_processor');
    ;
};
