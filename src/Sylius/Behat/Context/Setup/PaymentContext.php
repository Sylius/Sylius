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
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
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
     * @var ExampleFactoryInterface
     */
    private $paymentMethodExampleFactory;

    /**
     * @var FactoryInterface
     */
    private $paymentMethodTranslationFactory;

    /**
     * @var ObjectManager
     */
    private $paymentMethodManager;

    /**
     * @var array
     */
    private $gatewayFactories;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param PaymentMethodRepositoryInterface $paymentMethodRepository
     * @param ExampleFactoryInterface $paymentMethodExampleFactory
     * @param FactoryInterface $paymentMethodTranslationFactory
     * @param ObjectManager $paymentMethodManager
     * @param array $gatewayFactories
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ExampleFactoryInterface $paymentMethodExampleFactory,
        FactoryInterface $paymentMethodTranslationFactory,
        ObjectManager $paymentMethodManager,
        array $gatewayFactories
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentMethodExampleFactory = $paymentMethodExampleFactory;
        $this->paymentMethodTranslationFactory = $paymentMethodTranslationFactory;
        $this->paymentMethodManager = $paymentMethodManager;
        $this->gatewayFactories = $gatewayFactories;
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
     */
    public function theStoreHasAPaymentMethodWithACode($paymentMethodName, $paymentMethodCode)
    {
        $this->createPaymentMethod($paymentMethodName, $paymentMethodCode, 'Offline');
    }

    /**
     * @Given the store has a payment method :paymentMethodName with a code :paymentMethodCode and Paypal Express Checkout gateway
     */
    public function theStoreHasPaymentMethodWithCodeAndPaypalExpressCheckoutGateway(
        $paymentMethodName,
        $paymentMethodCode
    ) {
        $paymentMethod = $this->createPaymentMethod($paymentMethodName, $paymentMethodCode, 'Paypal Express Checkout');
        $paymentMethod->getGatewayConfig()->setConfig([
            'username' => 'TEST',
            'password' => 'TEST',
            'signature' => 'TEST',
            'payum.http_client' => '@sylius.payum.http_client',
            'sandbox' => true,
        ]);

        $this->paymentMethodManager->flush();
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

        $this->paymentMethodManager->flush();
    }

    /**
     * @Given the payment method :paymentMethod is disabled
     * @Given /^(this payment method) has been disabled$/
     */
    public function theStoreHasAPaymentMethodDisabled(PaymentMethodInterface $paymentMethod)
    {
        $paymentMethod->disable();

        $this->paymentMethodManager->flush();
    }

    /**
     * @Given /^(it) has instructions "([^"]+)"$/
     */
    public function itHasInstructions(PaymentMethodInterface $paymentMethod, $instructions)
    {
        $paymentMethod->setInstructions($instructions);

        $this->paymentMethodManager->flush();
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
        $gatewayFactory = array_search($gatewayFactory, $this->gatewayFactories);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodExampleFactory->create([
            'name' => ucfirst($name),
            'code' => $code,
            'description' => $description,
            'gatewayName' => $gatewayFactory,
            'gatewayFactory' => $gatewayFactory,
            'enabled' => true,
            'channels' => ($addForCurrentChannel && $this->sharedStorage->has('channel')) ? [$this->sharedStorage->get('channel')] : [],
        ]);

        if (null !== $position) {
            $paymentMethod->setPosition($position);
        }

        $this->sharedStorage->set('payment_method', $paymentMethod);
        $this->paymentMethodRepository->add($paymentMethod);

        return $paymentMethod;
    }
}
