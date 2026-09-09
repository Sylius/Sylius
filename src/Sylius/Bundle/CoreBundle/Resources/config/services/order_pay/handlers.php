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

use Sylius\Bundle\CoreBundle\OrderPay\Handler\PaymentStateFlashHandler;
use Sylius\Bundle\CoreBundle\OrderPay\Handler\PaymentStateFlashHandlerInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.handler.order_pay.payment_state_flash', PaymentStateFlashHandler::class)->abstract();
    $services->alias(PaymentStateFlashHandlerInterface::class, 'sylius.handler.order_pay.payment_state_flash');
};
