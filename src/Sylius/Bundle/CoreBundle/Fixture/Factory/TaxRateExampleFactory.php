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

use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\CustomerTaxCategoryInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
class TaxRateExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $taxRateFactory;

    /**
     * @var RepositoryInterface
     */
    private $zoneRepository;

    /**
     * @var RepositoryInterface
     */
    private $taxCategoryRepository;

    /**
     * @var RepositoryInterface
     */
    private $customerTaxCategoryRepository;

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
     * @param RepositoryInterface $customerTaxCategoryRepository
     */
    public function __construct(
        FactoryInterface $taxRateFactory,
        RepositoryInterface $zoneRepository,
        RepositoryInterface $taxCategoryRepository,
        RepositoryInterface $customerTaxCategoryRepository
    ) {
        $this->taxRateFactory = $taxRateFactory;
        $this->zoneRepository = $zoneRepository;
        $this->taxCategoryRepository = $taxCategoryRepository;
        $this->customerTaxCategoryRepository = $customerTaxCategoryRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): TaxRateInterface
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
        $taxRate->setCategory($options['category']);
        $taxRate->setCustomerTaxCategory($options['customer_tax_category']);

        return $taxRate;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('code', function (Options $options): string {
                return StringInflector::nameToCode($options['name']);
            })
            ->setDefault('name', function (Options $options): string {
                return $this->faker->words(3, true);
            })
            ->setDefault('amount', function (Options $options): float {
                return $this->faker->randomFloat(2, 0, 0.4);
            })
            ->setAllowedTypes('amount', 'float')
            ->setDefault('included_in_price', function (Options $options): bool {
                return $this->faker->boolean();
            })
            ->setAllowedTypes('included_in_price', 'bool')
            ->setDefault('calculator', 'default')
            ->setDefault('zone', LazyOption::randomOne($this->zoneRepository))
            ->setAllowedTypes('zone', ['null', 'string', ZoneInterface::class])
            ->setNormalizer('zone', LazyOption::findOneBy($this->zoneRepository, 'code'))
            ->setDefault('category', LazyOption::randomOne($this->taxCategoryRepository))
            ->setAllowedTypes('category', ['null', 'string', TaxCategoryInterface::class])
            ->setNormalizer('category', LazyOption::findOneBy($this->taxCategoryRepository, 'code'))
            ->setDefault('customer_tax_category', LazyOption::randomOne($this->customerTaxCategoryRepository))
            ->setAllowedTypes('customer_tax_category', ['null', 'string', CustomerTaxCategoryInterface::class])
            ->setNormalizer('customer_tax_category', LazyOption::findOneBy($this->customerTaxCategoryRepository, 'code'))
        ;
    }
}
