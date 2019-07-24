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

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Doctrine\Common\Persistence\ObjectManager;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

class OrderExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var FactoryInterface */
    protected $orderFactory;

    /** @var FactoryInterface */
    protected $orderItemFactory;

    /** @var OrderItemQuantityModifierInterface */
    protected $orderItemQuantityModifier;

    /** @var ObjectManager */
    protected $orderManager;

    /** @var RepositoryInterface */
    protected $channelRepository;

    /** @var RepositoryInterface */
    protected $customerRepository;

    /** @var RepositoryInterface */
    protected $productRepository;

    /** @var RepositoryInterface */
    protected $countryRepository;

    /** @var PaymentMethodRepositoryInterface */
    protected $paymentMethodRepository;

    /** @var ShippingMethodRepositoryInterface */
    protected $shippingMethodRepository;

    /** @var FactoryInterface */
    protected $addressFactory;

    /** @var StateMachineFactoryInterface */
    protected $stateMachineFactory;

    /** @var OrderShippingMethodSelectionRequirementCheckerInterface */
    protected $orderShippingMethodSelectionRequirementChecker;

    /** @var OrderPaymentMethodSelectionRequirementCheckerInterface */
    protected $orderPaymentMethodSelectionRequirementChecker;

    /** @var OptionsResolver */
    private $optionsResolver;

    /** @var \Faker\Generator */
    protected $faker;

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

        $this->optionsResolver = new OptionsResolver();
        $this->configureOptions($this->optionsResolver);
        $this->faker = \Faker\Factory::create();
    }

    public function create(array $options = []): OrderInterface
    {
        $options = $this->optionsResolver->resolve($options);

        return $this->createOrder($options['channel'], $options['customer'], $options['country']);
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('amount', 20)

            ->setDefault('channel', LazyOption::randomOne($this->channelRepository))
            ->setAllowedTypes('channel', ['null', 'string', ChannelInterface::class])
            ->setNormalizer('channel', LazyOption::findOneBy($this->channelRepository, 'code'))

            ->setDefault('customer', LazyOption::randomOne($this->customerRepository))
            ->setAllowedTypes('customer', ['null', 'string', CustomerInterface::class])
            ->setNormalizer('customer', LazyOption::findOneBy($this->customerRepository, 'email'))

            ->setDefault('country', LazyOption::randomOne($this->countryRepository))
            ->setAllowedTypes('country', ['null', 'string', CountryInterface::class])
            ->setNormalizer('country', LazyOption::findOneBy($this->countryRepository, 'code'))
        ;
    }

    protected function createOrder(ChannelInterface $channel, CustomerInterface $customer, CountryInterface $country): OrderInterface
    {
        $countryCode = $country->getCode();

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

        return $order;
    }

    protected function generateItems(OrderInterface $order): void
    {
        $numberOfItems = random_int(1, 5);
        $products = $this->productRepository->findAll();

        for ($i = 0; $i < $numberOfItems; ++$i) {
            /** @var OrderItemInterface $item */
            $item = $this->orderItemFactory->createNew();

            $product = $this->faker->randomElement($products);
            $variant = $this->faker->randomElement($product->getVariants()->toArray());

            $item->setVariant($variant);
            $this->orderItemQuantityModifier->modify($item, random_int(1, 5));

            $order->addItem($item);
        }
    }

    protected function address(OrderInterface $order, string $countryCode): void
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

    protected function selectShipping(OrderInterface $order): void
    {
        if (!$this->orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order)) {
            $this->applyCheckoutStateTransition($order, OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING);

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
        if (!$this->orderPaymentMethodSelectionRequirementChecker->isPaymentMethodSelectionRequired($order)) {
            $this->applyCheckoutStateTransition($order, OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT);
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

    protected function completeCheckout(OrderInterface $order): void
    {
        if ($this->faker->boolean(25)) {
            $order->setNotes($this->faker->sentence);
        }

        $this->applyCheckoutStateTransition($order, OrderCheckoutTransitions::TRANSITION_COMPLETE);
    }

    protected function applyCheckoutStateTransition(OrderInterface $order, string $transition): void
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
