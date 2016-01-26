<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Test\Services;

use Payum\Core\GatewayInterface;
use Payum\Core\Registry\RegistryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class PaymentMethodNameToGatewayConverter implements PaymentMethodNameToGatewayConverterInterface
{
    /**
     * @var RegistryInterface
     */
    private $payumRegistry;

    /**
     * @param RegistryInterface $payumRegistry
     */
    public function __construct(RegistryInterface $payumRegistry)
    {
        $this->payumRegistry = $payumRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function convert($paymentMethodName)
    {
        $this->validate($paymentMethodName);

        return $this->tryToMapPaymentMethodName($paymentMethodName);
    }

    /**
     * @return GatewayInterface[]
     */
    private function getRegisteredGateways()
    {
        return $this->payumRegistry->getGateways();
    }

    /**
     * @param string $paymentMethodName
     *
     * @throws \InvalidArgumentException
     */
    private function validate($paymentMethodName)
    {
        if (null == $paymentMethodName) {
            throw new \InvalidArgumentException(sprintf('Payment method name cannot be null'));
        }
    }

    /**
     * @param string $paymentMethodName
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    private function tryToMapPaymentMethodName($paymentMethodName)
    {
        $paymentMethodName = strtolower($paymentMethodName);
        $gateways = $this->getRegisteredGateways();

        foreach ($gateways as $gatewayKey => $gateway) {
            if ($this->convertGatewayKeyToName($gatewayKey) === $paymentMethodName) {
                return $gatewayKey;
            }
        }

        throw new \RuntimeException(sprintf('Cannot convert %s to gateway', $paymentMethodName));
    }

    /**
     * @param string $gatewayKey
     *
     * @return string
     */
    private function convertGatewayKeyToName($gatewayKey)
    {
        return str_replace('_', ' ', $gatewayKey);
    }
}
