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

use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class PaymentMethodFactory implements PaymentMethodFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $defaultFactory;

    /**
     * @var array
     */
    private $expectedGateways;

    /**
     * {@inheritdoc}
     */
    public function __construct(FactoryInterface $defaultFactory)
    {
        $this->defaultFactory = $defaultFactory;
        $this->expectedGateways = [
            'paypal_express_checkout' => 'PayPal Express Checkout',
            'be2bill_direct' => 'Be2bill Direct',
            'be2bill_offsite' => 'Be2bill Offsite',
            'stripe_checkout' => 'Stripe Checkout',
            'dummy' => 'Offline',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return $this->defaultFactory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createFromArray(array $parameters)
    {
        $this->checkGateway($parameters);
        $paymentMethod = $this->defaultFactory->createNew();

        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($parameters as $propertyName => $value) {
            $accessor->setValue($paymentMethod, $propertyName, $value);
        }

        return $paymentMethod;
    }

    /**
     * @param array $parameters
     *
     * @throws \InvalidArgumentException
     */
    private function checkGateway(array $parameters)
    {
        if (!isset($parameters['gateway'])) {
            throw new \InvalidArgumentException('Gateway parameter is not set');
        }

        if (!array_key_exists($parameters['gateway'], $this->expectedGateways)) {
            throw new \InvalidArgumentException(sprintf('There is no %s gateway registered, or update this check', $parameters['gateway']));
        }
    }
}
