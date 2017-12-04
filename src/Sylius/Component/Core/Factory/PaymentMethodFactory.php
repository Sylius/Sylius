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

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class PaymentMethodFactory implements PaymentMethodFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $decoratedFactory;

    /**
     * @var FactoryInterface
     */
    private $gatewayConfigFactory;

    /**
     * @param FactoryInterface $decoratedFactory
     * @param FactoryInterface $gatewayConfigFactory
     */
    public function __construct(FactoryInterface $decoratedFactory, FactoryInterface $gatewayConfigFactory)
    {
        $this->decoratedFactory = $decoratedFactory;
        $this->gatewayConfigFactory = $gatewayConfigFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(): PaymentMethodInterface
    {
        return $this->decoratedFactory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createWithGateway(string $gatewayFactory): PaymentMethodInterface
    {
        $gatewayConfig = $this->gatewayConfigFactory->createNew();
        $gatewayConfig->setFactoryName($gatewayFactory);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->decoratedFactory->createNew();
        $paymentMethod->setGatewayConfig($gatewayConfig);

        return $paymentMethod;
    }
}
