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

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\GatewayConfigInterface;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @template T of PaymentMethodInterface
 *
 * @implements PaymentMethodFactoryInterface<T>
 */
final class PaymentMethodFactory implements PaymentMethodFactoryInterface
{
    /**
     * @param FactoryInterface<T> $decoratedFactory
     * @param FactoryInterface<GatewayConfigInterface> $gatewayConfigFactory
     */
    public function __construct(private FactoryInterface $decoratedFactory, private FactoryInterface $gatewayConfigFactory)
    {
    }

    /** @inheritdoc */
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
