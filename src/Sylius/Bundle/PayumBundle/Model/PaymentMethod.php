<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PayumBundle\Model;

use Payum\Core\Model\GatewayConfigInterface;
use Sylius\Component\Payment\Model\PaymentMethod as BasePaymentMethod;
use Sylius\Component\Resource\Exception\UnsupportedMethodException;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PaymentMethod extends BasePaymentMethod implements PaymentMethodInterface
{
    /**
     * @var GatewayConfigInterface
     */
    protected $gatewayConfig;

    /**
     * {@inheritdoc}
     */
    public function setGatewayConfig(GatewayConfigInterface $gatewayConfig)
    {
        $this->gatewayConfig = $gatewayConfig;
    }

    /**
     * @return GatewayConfigInterface
     */
    public function getGatewayConfig()
    {
        return $this->gatewayConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getGateway()
    {
        if (null !== $this->gatewayConfig) {
            return $this->gatewayConfig->getGatewayName();
        }

        return null;
    }


    /**
     * {@inheritdoc}
     */
    public function setGateway($gateway)
    {
        throw new UnsupportedMethodException('setGateway');
    }
}
