<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Bundle\CoreBundle\Test\Services\PaymentMethodNameToGatewayConverterInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class PaymentMethodContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $paymentMethodFactory;

    /**
     * @var PaymentMethodNameToGatewayConverterInterface
     */
    private $paymentMethodNameToGatewayConverter;

    /**
     * @var PaymentMethodRepositoryInterface
     */
    private $paymentMethodRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $paymentMethodFactory
     * @param PaymentMethodNameToGatewayConverterInterface $paymentMethodNameToGatewayConverter
     * @param PaymentMethodRepositoryInterface $paymentMethodRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $paymentMethodFactory,
        PaymentMethodNameToGatewayConverterInterface $paymentMethodNameToGatewayConverter,
        PaymentMethodRepositoryInterface $paymentMethodRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->paymentMethodFactory = $paymentMethodFactory;
        $this->paymentMethodNameToGatewayConverter = $paymentMethodNameToGatewayConverter;
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    /**
     * @Given the store has a payment method :paymentMethodName with a code :paymentMethodCode
     */
    public function theStoreHasAPaymentMethodWithACode($paymentMethodName, $paymentMethodCode)
    {
        $paymentMethod = $this->createPaymentMethodFromNameAndCode($paymentMethodName, $paymentMethodCode);

        $this->sharedStorage->set('payment_method', $paymentMethod);
        $this->paymentMethodRepository->add($paymentMethod);
    }

    /**
     * @Given /^the store has a (payment method "([^"]*)") disabled$/
     */
    public function theStoreHasAPaymentMethodDisabled(PaymentMethodInterface $paymentMethod)
    {
        $paymentMethod->disable();

        $this->paymentMethodRepository->add($paymentMethod);
    }

    /**
     * @param string $name
     * @param string $code
     *
     * @return PaymentMethodInterface
     */
    private function createPaymentMethodFromNameAndCode($name, $code)
    {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodFactory->createNew();
        $paymentMethod->setName($name);
        $paymentMethod->setCode($code);
        $paymentMethod->setGateway($this->paymentMethodNameToGatewayConverter->convert($name));

        return $paymentMethod;
    }
}
