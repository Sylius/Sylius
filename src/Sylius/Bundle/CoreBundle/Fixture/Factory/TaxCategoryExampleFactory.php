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
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxCategoryExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var FactoryInterface */
    private $taxCategoryFactory;

    /** @var \Faker\Generator */
    private $faker;

    /** @var OptionsResolver */
    private $optionsResolver;

    public function __construct(FactoryInterface $taxCategoryFactory)
    {
        $this->taxCategoryFactory = $taxCategoryFactory;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): TaxCategoryInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var TaxCategoryInterface $taxCategory */
        $taxCategory = $this->taxCategoryFactory->createNew();

        $taxCategory->setCode($options['code']);
        $taxCategory->setName($options['name']);
        $taxCategory->setDescription($options['description']);

        return $taxCategory;
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
