<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Factory;

use Payum\Core\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class PaymentMethodFactory implements PaymentMethodFactoryInterface
{
    public function __construct(private FactoryInterface $decoratedFactory, private FactoryInterface $gatewayConfigFactory)
    {
    }

    public function createNew(): PaymentMethodInterface
    {
        return $this->decoratedFactory->createNew();
    }

    public function createWithGateway(string $gatewayFactory): PaymentMethodInterface
    {
        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $this->gatewayConfigFactory->createNew();
        $gatewayConfig->setFactoryName($gatewayFactory);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->decoratedFactory->createNew();
        $paymentMethod->setGatewayConfig($gatewayConfig);

        return $paymentMethod;
    }
}
