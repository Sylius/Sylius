<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TaxRateExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $taxRateFactory;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param FactoryInterface $taxRateFactory
     * @param RepositoryInterface $zoneRepository
     * @param RepositoryInterface $taxCategoryRepository
     */
    public function __construct(
        FactoryInterface $taxRateFactory,
        RepositoryInterface $zoneRepository,
        RepositoryInterface $taxCategoryRepository
    ) {
        $this->taxRateFactory = $taxRateFactory;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver =
            (new OptionsResolver())
                ->setDefault('name', function (Options $options) {
                    return $this->faker->words(3, true);
                })
                ->setDefault('code', function (Options $options) {
                    return StringInflector::nameToCode($options['name']);
                })
                ->setDefault('amount', function (Options $options) {
                    return $this->faker->randomFloat(2, 0, 1);
                })
                ->setAllowedTypes('amount', 'float')
                ->setDefault('included_in_price', function (Options $options) {
                    return $this->faker->boolean();
                })
                ->setAllowedTypes('included_in_price', 'bool')
                ->setDefault('calculator', 'default')
                ->setDefault('zone', LazyOption::randomOne($zoneRepository))
                ->setAllowedTypes('zone', ['null', 'string', ZoneInterface::class])
                ->setNormalizer('zone', LazyOption::findOneBy($zoneRepository, 'code'))
                ->setDefault('tax_category', LazyOption::randomOne($taxCategoryRepository))
                ->setAllowedTypes('tax_category', ['null', 'string', TaxCategoryInterface::class])
                ->setNormalizer('tax_category', LazyOption::findOneBy($taxCategoryRepository, 'code'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var TaxRateInterface $taxRate */
        $taxRate = $this->taxRateFactory->createNew();

        $taxRate->setCode($options['code']);
        $taxRate->setName($options['name']);
        $taxRate->setAmount($options['amount']);
        $taxRate->setIncludedInPrice($options['included_in_price']);
        $taxRate->setCalculator($options['calculator']);
        $taxRate->setZone($options['zone']);
        $taxRate->setCategory($options['tax_category']);

        return $taxRate;
    }
}
