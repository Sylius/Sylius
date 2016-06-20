<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TaxRateFixture extends AbstractResourceFixture
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
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @param FactoryInterface $taxRateFactory
     * @param ObjectManager $taxRateManager
     * @param RepositoryInterface $zoneRepository
     * @param RepositoryInterface $taxCategoryRepository
     */
    public function __construct(
        FactoryInterface $taxRateFactory,
        ObjectManager $taxRateManager,
        RepositoryInterface $zoneRepository,
        RepositoryInterface $taxCategoryRepository
    ) {
        parent::__construct($taxRateManager, 'tax_rates', 'name');

        $this->taxRateFactory = $taxRateFactory;
        $this->zoneRepository = $zoneRepository;
        $this->taxCategoryRepository = $taxCategoryRepository;

        $this->faker = \Faker\Factory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tax_rate';
    }

    /**
     * {@inheritdoc}
     */
    protected function loadResource(array $options)
    {
        /** @var TaxRateInterface $taxRate */
        $taxRate = $this->taxRateFactory->createNew();

        $taxRate->setCode($options['code']);
        $taxRate->setName($options['name']);
        $taxRate->setAmount($options['amount']);
        $taxRate->setIncludedInPrice($options['included_in_price']);
        $taxRate->setCalculator($options['calculator']);
        $taxRate->setZone($options['zone']);
        $taxRate->setCategory($options['category']);

        return $taxRate;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode)
    {
        $resourceNode
            ->children()
            ->scalarNode('code')->cannotBeEmpty()->end()
            ->floatNode('amount')->cannotBeEmpty()->end()
            ->booleanNode('included_in_price')->end()
            ->scalarNode('calculator')->cannotBeEmpty()->end()
            ->scalarNode('zone')->cannotBeEmpty()->end()
            ->scalarNode('category')->cannotBeEmpty()->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceOptionsResolver(array $options, OptionsResolver $optionsResolver)
    {
        $optionsResolver
            ->setRequired('name')
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
            ->setDefault('zone', null)
            ->setAllowedTypes('zone', ['null', ZoneInterface::class])
            ->setNormalizer('zone', static::createResourceNormalizer($this->zoneRepository))
            ->setDefault('category', null)
            ->setAllowedTypes('category', ['null', TaxCategoryInterface::class])
            ->setNormalizer('category', static::createResourceNormalizer($this->taxCategoryRepository))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateResourcesOptions($amount)
    {
        $resourcesOptions = [];
        for ($i = 0; $i < $amount; ++$i) {
            $resourcesOptions[] = ['name' => $this->faker->words(3, true)];
        }

        return $resourcesOptions;
    }
}
