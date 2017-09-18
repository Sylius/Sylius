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
use Sylius\Component\Core\Model\CustomerTaxCategoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerTaxCategoryExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $customerTaxCategoryFactory;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param FactoryInterface $customerTaxCategoryFactory
     */
    public function __construct(FactoryInterface $customerTaxCategoryFactory)
    {
        $this->customerTaxCategoryFactory = $customerTaxCategoryFactory;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): CustomerTaxCategoryInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var CustomerTaxCategoryInterface $customerTaxCategory */
        $customerTaxCategory = $this->customerTaxCategoryFactory->createNew();

        $customerTaxCategory->setCode($options['code']);
        $customerTaxCategory->setName($options['name']);
        $customerTaxCategory->setDescription($options['description']);

        return $customerTaxCategory;
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
            ->setDefault('description', function (Options $options): string {
                return $this->faker->paragraph;
            })
        ;
    }
}
