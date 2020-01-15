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
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class OrderFixture extends AbstractFixture
{
    /** @var OrderExampleFactory */
    protected $orderExampleFactory;

    /** @var ObjectManager */
    protected $orderManager;

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
        if ($orderExampleFactory === null) {
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
                $orderPaymentMethodSelectionRequirementChecker
            );

            @trigger_error('Use orderExampleFactory. OrderFixture is deprecated since 1.6 and will be prohibited since 2.0.', \E_USER_DEPRECATED);
        }

        $this->orderManager = $orderManager;
        $this->orderExampleFactory = $orderExampleFactory;

        $this->faker = \Faker\Factory::create();
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
