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
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\CoreBundle\Test\Services\PaymentMethodNameToGatewayConverterInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentMethodTranslationInterface;
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
     * @var FactoryInterface
     */
    private $paymentMethodTranslationFactory;

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
     * @param FactoryInterface $paymentMethodTranslationFactory
     * @param PaymentMethodNameToGatewayConverterInterface $paymentMethodNameToGatewayConverter
     * @param ObjectManager $objectManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        FactoryInterface $paymentMethodFactory,
        FactoryInterface $paymentMethodTranslationFactory,
        PaymentMethodNameToGatewayConverterInterface $paymentMethodNameToGatewayConverter,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentMethodFactory = $paymentMethodFactory;
        $this->paymentMethodTranslationFactory = $paymentMethodTranslationFactory;
        $this->paymentMethodNameToGatewayConverter = $paymentMethodNameToGatewayConverter;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given the store allows paying :paymentMethodName
     * @Given the store allows paying with :paymentMethodName
     */
    public function storeAllowsPaying($paymentMethodName)
    {
        $this->createPaymentMethodFromNameAndCode($paymentMethodName, 'PM_'.$paymentMethodName, 'Payment method');
    }

    /**
     * @Given the store has a payment method :paymentMethodName with a code :paymentMethodCode
     */
    public function theStoreHasAPaymentMethodWithACode($paymentMethodName, $paymentMethodCode)
    {
        $this->createPaymentMethodFromNameAndCode($paymentMethodName, $paymentMethodCode);
    }

    /**
     * @Given /^(this payment method) is named "([^"]+)" in the "([^"]+)" locale$/
     */
    public function thisPaymentMethodIsNamedIn(PaymentMethodInterface $paymentMethod, $name, $locale)
    {
        /** @var PaymentMethodTranslationInterface $translation */
        $translation = $this->paymentMethodTranslationFactory->createNew();
        $translation->setLocale($locale);
        $translation->setName($name);

        $paymentMethod->addTranslation($translation);

        $this->objectManager->flush();
    }

    /**
     * @Given the payment method :paymentMethod is disabled
     * @Given /^(this payment method) is disabled$/
     */
    public function theStoreHasAPaymentMethodDisabled(PaymentMethodInterface $paymentMethod)
    {
        $paymentMethod->disable();

        $this->objectManager->flush();
    }

    /**
     * @Given /^(it) has instructions "([^"]+)"$/
     */
    public function itHasInstructions(PaymentMethodInterface $paymentMethod, $instructions)
    {
        $paymentMethod->setInstructions($instructions);

        $this->objectManager->flush();
    }

    /**
     * @Given the store has :paymentMethodName payment method not assigned to any channel
     */
    public function theStoreHasPaymentMethodNotAssignedToAnyChannel($paymentMethodName)
    {
        $this->createPaymentMethodFromNameAndCode($paymentMethodName, 'PM_'.$paymentMethodName, 'Payment method', false);
    }

    /**
     * @param string $name
     * @param string $code
     * @param bool $addForCurrentChannel
     * @param string $description
     */
    private function createPaymentMethodFromNameAndCode($name, $code, $description = '', $addForCurrentChannel = true)
    {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodFactory->createNew();
        $paymentMethod->setName(ucfirst($name));
        $paymentMethod->setCode($code);
        $paymentMethod->setGateway($this->paymentMethodNameToGatewayConverter->convert($name));
        $paymentMethod->setDescription($description);

        if ($addForCurrentChannel && $this->sharedStorage->has('channel')) {
            $channel = $this->sharedStorage->get('channel');
            $channel->addPaymentMethod($paymentMethod);
        }

        $this->sharedStorage->set('payment_method', $paymentMethod);
        $this->paymentMethodRepository->add($paymentMethod);
    }
}
