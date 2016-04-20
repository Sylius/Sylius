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
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Test\Services\PaymentMethodNameToGatewayConverterInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class PaymentContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var PaymentMethodRepositoryInterface
     */
    private $paymentMethodRepository;

    /**
     * @var FactoryInterface
     */
    private $paymentMethodFactory;

    /**
     * @var PaymentMethodNameToGatewayConverterInterface
     */
    private $paymentMethodNameToGatewayConverter;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param PaymentMethodRepositoryInterface $paymentMethodRepository
     * @param FactoryInterface $paymentMethodFactory
     * @param PaymentMethodNameToGatewayConverterInterface $paymentMethodNameToGatewayConverter
     * @param ObjectManager $objectManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        FactoryInterface $paymentMethodFactory,
        PaymentMethodNameToGatewayConverterInterface $paymentMethodNameToGatewayConverter,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentMethodFactory = $paymentMethodFactory;
        $this->paymentMethodNameToGatewayConverter = $paymentMethodNameToGatewayConverter;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given the store allows paying :paymentMethodName
     * @Given the store allows paying with :paymentMethodName
     */
    public function storeAllowsPaying($paymentMethodName)
    {
        $paymentMethod = $this->paymentMethodFactory->createNew();
        $paymentMethod->setCode('PM_'.$paymentMethodName);
        $paymentMethod->setName(ucfirst($paymentMethodName));
        $paymentMethod->setGateway($this->paymentMethodNameToGatewayConverter->convert($paymentMethodName));
        $paymentMethod->setDescription('Payment method');

        $channel = $this->sharedStorage->get('channel');
        $channel->addPaymentMethod($paymentMethod);

        $this->paymentMethodRepository->add($paymentMethod);
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
     * @Given the payment method :paymentMethod is disabled
     */
    public function theStoreHasAPaymentMethodDisabled(PaymentMethodInterface $paymentMethod)
    {
        $paymentMethod->disable();

        $this->objectManager->flush();
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
