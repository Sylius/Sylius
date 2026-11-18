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

use Sylius\Bundle\CoreBundle\OrderPay\Resolver\PaymentToPayResolver;
use Sylius\Component\Payment\Model\PaymentInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_shop.resolver.order_pay.payment_to_pay', PaymentToPayResolver::class)
        ->args([PaymentInterface::STATE_NEW])
    ;
};
