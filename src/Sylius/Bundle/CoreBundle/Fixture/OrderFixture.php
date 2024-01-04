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
use Sylius\Bundle\CoreBundle\Fixture\Factory\OrderExampleFactory;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class OrderFixture extends AbstractFixture
{
    private Generator $faker;

    public function __construct(
        protected ObjectManager $orderManager,
        protected OrderExampleFactory $orderExampleFactory,
    ) {
        $this->faker = Factory::create();
    }

    /**
     * @param array<string, mixed> $options
     */
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

    /**
     * @return \DateTimeInterface[]
     */
    protected function generateDates(int $amount): array
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
