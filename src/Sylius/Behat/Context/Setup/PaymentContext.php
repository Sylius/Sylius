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
use Sylius\Component\Core\Factory\PaymentMethodFactoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\PaymentMethodInterface;
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
     * @var PaymentMethodFactoryInterface
     */
    private $paymentMethodFactory;

    /**
     * @var FactoryInterface
     */
    private $paymentMethodTranslationFactory;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var array
     */
    private $gatewayFactories;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param PaymentMethodRepositoryInterface $paymentMethodRepository
     * @param PaymentMethodFactoryInterface $paymentMethodFactory
     * @param FactoryInterface $paymentMethodTranslationFactory
     * @param ObjectManager $objectManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodFactoryInterface $paymentMethodFactory,
        FactoryInterface $paymentMethodTranslationFactory,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentMethodFactory = $paymentMethodFactory;
        $this->paymentMethodTranslationFactory = $paymentMethodTranslationFactory;
        $this->objectManager = $objectManager;
        $this->gatewayFactories = [
            'offline' => 'Offline',
            'paypal_express_checkout' => 'Paypal Express Checkout',
            'stripe_checkout' => 'Stripe Checkout',
        ];
    }

    /**
     * @Given the store (also )allows paying (with ):paymentMethodName
     * @Given the store (also )allows paying with :paymentMethodName at position :position
     */
    public function storeAllowsPaying($paymentMethodName, $position = null)
    {
        $this->createPaymentMethod($paymentMethodName, 'PM_'.$paymentMethodName, 'Offline', 'Payment method', true, $position);
    }

    /**
     * @Given /^the store allows paying (\w+) for (all channels)$/
     */
    public function storeAllowsPayingForAllChannels($paymentMethodName, array $channels)
    {
        $paymentMethod = $this->createPaymentMethod($paymentMethodName, StringInflector::nameToUppercaseCode($paymentMethodName), 'Offline', 'Payment method', false);

        foreach ($channels as $channel) {
            $paymentMethod->addChannel($channel);
        }
    }

    /**
     * @Given the store has a payment method :paymentMethodName with a code :paymentMethodCode
     * @Given the store has a payment method :paymentMethodName with a code :paymentMethodCode and gateway factory :gatewayFactory
     */
    public function theStoreHasAPaymentMethodWithACode($paymentMethodName, $paymentMethodCode, $gatewayFactory = 'Offline')
    {
        $this->createPaymentMethod($paymentMethodName, $paymentMethodCode, $gatewayFactory);
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
     * @Given /^(this payment method) has been disabled$/
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
        $this->createPaymentMethod($paymentMethodName, 'PM_'.$paymentMethodName, 'Payment method', 'Offline', false);
    }

    /**
     * @param string $name
     * @param string $code
     * @param string $gatewayFactory
     * @param string $description
     * @param bool $addForCurrentChannel
     * @param int|null $position
     *
     * @return PaymentMethodInterface
     */
    private function createPaymentMethod(
        $name,
        $code,
        $gatewayFactory = 'Offline',
        $description = '',
        $addForCurrentChannel = true,
        $position = null
    ) {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodFactory->createWithGateway(array_search($gatewayFactory, $this->gatewayFactories));
        $paymentMethod->getGatewayConfig()->setGatewayName($gatewayFactory);
        $paymentMethod->setName(ucfirst($name));
        $paymentMethod->setCode($code);
        $paymentMethod->setPosition($position);
        $paymentMethod->setDescription($description);

        if ($addForCurrentChannel && $this->sharedStorage->has('channel')) {
            $paymentMethod->addChannel($this->sharedStorage->get('channel'));
        }

        $this->sharedStorage->set('payment_method', $paymentMethod);
        $this->paymentMethodRepository->add($paymentMethod);

        return $paymentMethod;
    }
}
