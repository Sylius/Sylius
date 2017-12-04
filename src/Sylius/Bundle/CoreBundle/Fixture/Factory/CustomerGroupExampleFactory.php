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

use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerGroupExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $customerGroupFactory;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param FactoryInterface $customerGroupFactory
     */
    public function __construct(FactoryInterface $customerGroupFactory)
    {
        $this->customerGroupFactory = $customerGroupFactory;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): CustomerGroupInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var CustomerGroupInterface $customerGroup */
        $customerGroup = $this->customerGroupFactory->createNew();
        $customerGroup->setCode($options['code']);
        $customerGroup->setName($options['name']);

        return $customerGroup;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('name', function (Options $options): string {
                return $this->faker->words(3, true);
            })
            ->setDefault('code', function (Options $options): string {
                return StringInflector::nameToCode($options['name']);
            })
        ;
    }
}
