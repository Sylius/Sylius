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
use Sylius\Bundle\CoreBundle\Fixture\Factory\OrderExampleFactory;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

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

    /** @var OrderShippingMethodSelectionRequirementCheckerInterface */
    private $orderShippingMethodSelectionRequirementChecker;

    /** @var OrderPaymentMethodSelectionRequirementCheckerInterface */
    private $orderPaymentMethodSelectionRequirementChecker;

    /** @var OrderExampleFactory */
    private $orderExampleFactory;

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
        StateMachineFactoryInterface $stateMachineFactory,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
        OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker,
        OrderExampleFactory $orderExampleFactory = null
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

        $this->orderExampleFactory = $orderExampleFactory;
        if ($orderExampleFactory === null) {
            $this->orderExampleFactory = $this->createOrderExampleFactory();

            @trigger_error('Use orderExampleFactory. OrderFixture is deprecated since 1.6 and will be prohibited since 2.0.', \E_USER_DEPRECATED);
        }

        $this->faker = \Faker\Factory::create();
    }

    public function load(array $options): void
    {
        $randomDates = $this->generateDates($options['amount']);

        for ($i = 0; $i < $options['amount']; ++$i) {

            $order = $this->orderExampleFactory->create($options);
            $this->setOrderCompletedDate($order, array_shift($randomDates));

            $this->orderManager->persist($order);

            if (0 === ($i % 50)) {
                $this->orderManager->flush();
            }
        }

        $this->orderManager->flush();
    }

    public function getName(): string
    {
        return 'order';
    }

    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
                ->integerNode('amount')->isRequired()->min(0)->end()
                ->scalarNode('channel')->cannotBeEmpty()->end()
                ->scalarNode('customer')->cannotBeEmpty()->end()
                ->scalarNode('country')->cannotBeEmpty()->end()
            ->end()
        ;
    }

    private function createOrderExampleFactory() {
        return new OrderExampleFactory(
            $this->orderFactory,
            $this->orderItemFactory,
            $this->orderItemQuantityModifier,
            $this->orderManager,
            $this->channelRepository,
            $this->customerRepository,
            $this->productRepository,
            $this->paymentMethodRepository,
            $this->countryRepository,
            $this->shippingMethodRepository,
            $this->addressFactory,
            $this->stateMachineFactory,
            $this->orderShippingMethodSelectionRequirementChecker,
            $this->orderPaymentMethodSelectionRequirementChecker
        );
    }

    private function setOrderCompletedDate(OrderInterface $order, \DateTimeInterface $date): void
    {
        if ($order->getCheckoutState() === OrderCheckoutStates::STATE_COMPLETED) {
            $order->setCheckoutCompletedAt($date);
        }
    }

    private function generateDates(int $amount): array
    {
        $dates = [];

        for ($i = 0; $i < $amount; ++$i) {
            /** @var \DateTimeInterface|array $dates */
            $dates[] = $this->faker->dateTimeBetween('-1 years', 'now');
        }
        sort($dates);

        return $dates;
    }
}
