<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\OrderExampleFactory;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Webmozart\Assert\Assert;

class OrderFixture extends AbstractFixture
{
    /** @var OrderExampleFactory */
    protected $orderExampleFactory;

    /** @var ObjectManager */
    protected $orderManager;

    private Generator $faker;

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
        StateMachineFactoryInterface|StateMachineInterface $stateMachineFactory,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
        OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker,
        ?OrderExampleFactory $orderExampleFactory = null,
    ) {
        if ($orderExampleFactory === null) {
            Assert::isInstanceOf($productRepository, ProductRepositoryInterface::class);

            $orderExampleFactory = new OrderExampleFactory(
                $orderFactory,
                $orderItemFactory,
                $orderItemQuantityModifier,
                $orderManager,
                $channelRepository,
                $customerRepository,
                $productRepository,
                $countryRepository,
                $paymentMethodRepository,
                $shippingMethodRepository,
                $addressFactory,
                $stateMachineFactory,
                $orderShippingMethodSelectionRequirementChecker,
                $orderPaymentMethodSelectionRequirementChecker,
            );

            trigger_deprecation(
                'sylius/core-bundle',
                '1.6',
                'Use OrderExampleFactory. OrderFixture is deprecated and will be prohibited since Sylius 2.0.',
            );
        }

        $this->orderManager = $orderManager;
        $this->orderExampleFactory = $orderExampleFactory;

        $this->faker = Factory::create();
    }

    public function load(array $options): void
    {
        $generateDates = $this->generateDates($options['amount']);

        for ($i = 0; $i < $options['amount']; ++$i) {
            $options = array_merge($options, ['complete_date' => array_shift($generateDates)]);

            $order = $this->orderExampleFactory->create($options);

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
                ->booleanNode('fulfilled')->defaultValue(false)->end()
            ->end()
        ;
    }

    private function generateDates(int $amount): array
    {
        /** @var \DateTimeInterface[] $dates */
        $dates = [];

        for ($i = 0; $i < $amount; ++$i) {
            $dates[] = $this->faker->dateTimeBetween('-1 years', 'now');
        }

        sort($dates);

        return $dates;
    }
}
