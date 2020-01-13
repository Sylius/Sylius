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

namespace Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Webmozart\Assert\Assert;

class OrderFixture extends AbstractFixture
{
    /** @var FactoryInterface */
    private $orderFactory;

    /** @var FactoryInterface */
    private $orderItemFactory;

    /** @var OrderItemQuantityModifierInterface */
    private $orderItemQuantityModifier;

    /** @var ObjectManager */
    private $orderManager;

    /** @var RepositoryInterface */
    private $channelRepository;

    /** @var RepositoryInterface */
    private $customerRepository;

    /** @var RepositoryInterface */
    private $productRepository;

    /** @var RepositoryInterface */
    private $countryRepository;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    /** @var ShippingMethodRepositoryInterface */
    private $shippingMethodRepository;

    /** @var FactoryInterface */
    private $addressFactory;

    /** @var StateMachineFactoryInterface */
    private $stateMachineFactory;

    /** @var \Faker\Generator */
    private $faker;

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
        StateMachineFactoryInterface $stateMachineFactory
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

        $this->faker = \Faker\Factory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options): void
    {
        $channels = $this->channelRepository->findAll();
        $customers = $this->customerRepository->findAll();
        $countries = $this->countryRepository->findAll();

        for ($i = 0; $i < $options['amount']; ++$i) {
            $channel = $this->faker->randomElement($channels);
            $customer = $this->faker->randomElement($customers);
            $countryCode = $this->faker->randomElement($countries)->getCode();

            $currencyCode = $channel->getBaseCurrency()->getCode();
            $localeCode = $this->faker->randomElement($channel->getLocales()->toArray())->getCode();

            /** @var OrderInterface $order */
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
    public function getName(): string
    {
        return 'order';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
                ->integerNode('amount')->isRequired()->min(0)->end()
        ;
    }

    private function generateItems(OrderInterface $order): void
    {
        $numberOfItems = random_int(1, 5);
        $products = $this->productRepository->findAll();
        $generatedItems = [];

        for ($i = 0; $i < $numberOfItems; ++$i) {
            /** @var ProductInterface $product */
            $product = $this->faker->randomElement($products);

            if (!$product->hasChannel($order->getChannel())) {
                --$i;

                continue;
            }

            $variant = $this->faker->randomElement($product->getVariants()->toArray());

            if (array_key_exists($variant->getCode(), $generatedItems)) {
                /** @var OrderItemInterface $item */
                $item = $generatedItems[$variant->getCode()];
                $this->orderItemQuantityModifier->modify($item, $item->getQuantity() + random_int(1, 5));

                continue;
            }

            /** @var OrderItemInterface $item */
            $item = $this->orderItemFactory->createNew();

            $item->setVariant($variant);
            $this->orderItemQuantityModifier->modify($item, random_int(1, 5));

            $generatedItems[$variant->getCode()] = $item;
            $order->addItem($item);
        }
    }

    private function address(OrderInterface $order, string $countryCode): void
    {
        /** @var AddressInterface $address */
        $address = $this->addressFactory->createNew();
        $address->setFirstName($this->faker->firstName);
        $address->setLastName($this->faker->lastName);
        $address->setStreet($this->faker->streetAddress);
        $address->setCountryCode($countryCode);
        $address->setCity($this->faker->city);
        $address->setPostcode($this->faker->postcode);

        $order->setShippingAddress($address);
        $order->setBillingAddress(clone $address);

        $this->applyCheckoutStateTransition($order, OrderCheckoutTransitions::TRANSITION_ADDRESS);
    }

    private function selectShipping(OrderInterface $order): void
    {
        if ($order->getCheckoutState() === OrderCheckoutStates::STATE_SHIPPING_SKIPPED) {
            return;
        }

        $channel = $order->getChannel();
        $shippingMethods = $this->shippingMethodRepository->findEnabledForChannel($channel);

        if (count($shippingMethods) === 0) {
            throw new \InvalidArgumentException(sprintf(
                'You have no shipping method available for the channel with code "%s", but they are required to proceed an order',
                $channel->getCode()
            ));
        }

        $shippingMethod = $this->faker->randomElement($shippingMethods);

        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();
        Assert::notNull($shippingMethod, $this->generateInvalidSkipMessage('shipping', $channel->getCode()));

        foreach ($order->getShipments() as $shipment) {
            $shipment->setMethod($shippingMethod);
        }

        $this->applyCheckoutStateTransition($order, OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);
    }

    private function selectPayment(OrderInterface $order): void
    {
        if ($order->getCheckoutState() === OrderCheckoutStates::STATE_PAYMENT_SKIPPED) {
            return;
        }

        $paymentMethod = $this
            ->faker
            ->randomElement($this->paymentMethodRepository->findEnabledForChannel($order->getChannel()))
        ;

        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();
        Assert::notNull($paymentMethod, $this->generateInvalidSkipMessage('payment', $channel->getCode()));

        foreach ($order->getPayments() as $payment) {
            $payment->setMethod($paymentMethod);
        }

        $this->applyCheckoutStateTransition($order, OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);
    }

    private function completeCheckout(OrderInterface $order): void
    {
        if ($this->faker->boolean(25)) {
            $order->setNotes($this->faker->sentence);
        }

        $this->applyCheckoutStateTransition($order, OrderCheckoutTransitions::TRANSITION_COMPLETE);
    }

    private function applyCheckoutStateTransition(OrderInterface $order, string $transition): void
    {
        $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->apply($transition);
    }

    private function generateInvalidSkipMessage(string $type, string $channelCode): string
    {
        return sprintf(
            "No enabled %s method was found for the channel '%s'. " .
            "Set 'skipping_%s_step_allowed' option to true for this channel if you want to skip %s method selection.",
            $type, $channelCode, $type, $type
        );
    }
}
