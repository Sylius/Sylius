<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderFixture extends AbstractFixture
{
    /**
     * @var FactoryInterface
     */
    private $orderFactory;

    /**
     * @var FactoryInterface
     */
    private $orderItemFactory;

    /**
     * @var OrderItemQuantityModifierInterface
     */
    private $orderItemQuantityModifier;

    /**
     * @var ObjectManager
     */
    private $orderManager;

    /**
     * @var RepositoryInterface
     */
    private $channelRepository;

    /**
     * @var RepositoryInterface
     */
    private $customerRepository;

    /**
     * @var RepositoryInterface
     */
    private $productRepository;

    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * @var PaymentMethodRepositoryInterface
     */
    private $paymentMethodRepository;

    /**
     * @var ShippingMethodRepositoryInterface
     */
    private $shippingMethodRepository;

    /**
     * @var FactoryInterface
     */
    private $addressFactory;

    /**
     * @var StateMachineFactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @var OrderShippingMethodSelectionRequirementCheckerInterface
     */
    private $orderShippingMethodSelectionRequirementChecker;

    /**
     * @var OrderPaymentMethodSelectionRequirementCheckerInterface
     */
    private $orderPaymentMethodSelectionRequirementChecker;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @param FactoryInterface $orderFactory
     * @param FactoryInterface $orderItemFactory
     * @param OrderItemQuantityModifierInterface $orderItemQuantityModifier
     * @param ObjectManager $orderManager
     * @param RepositoryInterface $channelRepository
     * @param RepositoryInterface $customerRepository
     * @param RepositoryInterface $productRepository
     * @param RepositoryInterface $countryRepository
     * @param PaymentMethodRepositoryInterface $paymentMethodRepository
     * @param ShippingMethodRepositoryInterface $shippingMethodRepository
     * @param FactoryInterface $addressFactory
     * @param StateMachineFactoryInterface $stateMachineFactory
     * @param OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker
     * @param OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker
     */
    public function __construct(
        FactoryInterface $orderFactory,
        FactoryInterface $orderItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        ObjectManager $orderManager,
        RepositoryInterface $channelRepository,
        RepositoryInterface $customerRepository,
        RepositoryInterface $productRepository,
        RepositoryInterface $countryRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        FactoryInterface $addressFactory,
        StateMachineFactoryInterface $stateMachineFactory,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
        OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker
    ) {
        $this->orderFactory = $orderFactory;
        $this->orderItemFactory = $orderItemFactory;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->orderManager = $orderManager;
        $this->channelRepository = $channelRepository;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->countryRepository = $countryRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->addressFactory = $addressFactory;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->orderShippingMethodSelectionRequirementChecker = $orderShippingMethodSelectionRequirementChecker;
        $this->orderPaymentMethodSelectionRequirementChecker = $orderPaymentMethodSelectionRequirementChecker;

        $this->faker = \Faker\Factory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options)
    {
        $channels = $this->channelRepository->findAll();
        $customers = $this->customerRepository->findAll();
        $countries = $this->countryRepository->findAll();

        for ($i = 0; $i < $options['amount']; $i++) {
            $channel = $this->faker->randomElement($channels);
            $customer = $this->faker->randomElement($customers);
            $countryCode = $this->faker->randomElement($countries)->getCode();

            $currencyCode = $channel->getBaseCurrency()->getCode();
            $localeCode = $this->faker->randomElement($channel->getLocales()->toArray())->getCode();

            $order = $this->orderFactory->createNew();
            $order->setChannel($channel);
            $order->setCustomer($customer);
            $order->setCurrencyCode($currencyCode);
            $order->setLocaleCode($localeCode);

            $this->generateItems($order);

            $this->address($order, $countryCode);
            $this->selectShipping($order);
            $this->selectPayment($order);
            $this->completeCheckout($order);

            $this->orderManager->persist($order);

            if (0 === ($i % 50)) {
                $this->orderManager->flush();
            }
        }

        $this->orderManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'order';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode)
    {
        $optionsNode
            ->children()
                ->integerNode('amount')->isRequired()->min(0)->end()
        ;
    }

    /**
     * @param OrderInterface $order
     */
    private function generateItems(OrderInterface $order)
    {
        $numberOfItems = rand(1, 5);
        $products = $this->productRepository->findAll();

        for ($i = 0; $i < $numberOfItems; $i++) {
            $item = $this->orderItemFactory->createNew();

            $product = $this->faker->randomElement($products);
            $variant = $this->faker->randomElement($product->getVariants()->toArray());

            $item->setVariant($variant);
            $this->orderItemQuantityModifier->modify($item, rand(1, 5));

            $order->addItem($item);
        }
    }

    /**
     * @param OrderInterface $order
     * @param string $countryCode
     */
    private function address(OrderInterface $order, $countryCode)
    {
        /** @var AddressInterface $address */
        $address = $this->addressFactory->createNew();
        $address->setFirstname($this->faker->firstName);
        $address->setLastname($this->faker->lastName);
        $address->setStreet($this->faker->streetName);
        $address->setCountryCode($countryCode);
        $address->setCity($this->faker->city);
        $address->setPostcode($this->faker->postcode);

        $order->setShippingAddress($address);
        $order->setBillingAddress(clone $address);

        $this->applyCheckoutStateTransition($order, OrderCheckoutTransitions::TRANSITION_ADDRESS);
    }

    /**
     * @param OrderInterface $order
     */
    private function selectShipping(OrderInterface $order)
    {
        $shippingMethod = $this
            ->faker
            ->randomElement($this->shippingMethodRepository->findEnabledForChannel($order->getChannel()))
        ;
        Assert::notNull($shippingMethod, 'Shipping method should not be null.');

        foreach ($order->getShipments() as $shipment) {
            $shipment->setMethod($shippingMethod);
        }

        if ($this->orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order)) {
            $this->applyCheckoutStateTransition($order, OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);
        }
    }

    /**
     * @param OrderInterface $order
     */
    private function selectPayment(OrderInterface $order)
    {
        $paymentMethod = $this
            ->faker
            ->randomElement($this->paymentMethodRepository->findEnabledForChannel($order->getChannel()))
        ;
        Assert::notNull($paymentMethod, 'Payment method should not be null.');

        foreach ($order->getPayments() as $payment) {
            $payment->setMethod($paymentMethod);
        }

        if ($this->orderPaymentMethodSelectionRequirementChecker->isPaymentMethodSelectionRequired($order)) {
            $this->applyCheckoutStateTransition($order, OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);
        }
    }

    /**
     * @param OrderInterface $order
     */
    private function completeCheckout(OrderInterface $order)
    {
        if ($this->faker->boolean(25)) {
            $order->setNotes($this->faker->sentence);
        }

        $this->applyCheckoutStateTransition($order, OrderCheckoutTransitions::TRANSITION_COMPLETE);
    }

    /**
     * @param OrderInterface $order
     * @param string $transition
     */
    private function applyCheckoutStateTransition(OrderInterface $order, $transition)
    {
        $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->apply($transition);
    }
}
