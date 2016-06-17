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
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Calculator\DefaultCalculators;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ShippingMethodFixture extends AbstractResourceFixture
{
    /**
     * @var FactoryInterface
     */
    private $shippingMethodFactory;

    /**
     * @var RepositoryInterface
     */
    private $zoneRepository;

    /**
     * @var RepositoryInterface
     */
    private $shippingCategoryRepository;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @param FactoryInterface $shippingMethodFactory
     * @param ObjectManager $shippingMethodManager
     * @param RepositoryInterface $zoneRepository
     * @param RepositoryInterface $shippingCategoryRepository
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(
        FactoryInterface $shippingMethodFactory,
        ObjectManager $shippingMethodManager,
        RepositoryInterface $zoneRepository,
        RepositoryInterface $shippingCategoryRepository,
        RepositoryInterface $localeRepository
    ) {
        parent::__construct($shippingMethodManager, 'shipping_methods', 'name');

        $this->shippingMethodFactory = $shippingMethodFactory;
        $this->zoneRepository = $zoneRepository;
        $this->shippingCategoryRepository = $shippingCategoryRepository;
        $this->localeRepository = $localeRepository;

        $this->faker = \Faker\Factory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'shipping_method';
    }

    /**
     * {@inheritdoc}
     */
    protected function loadResource(array $options)
    {
        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $this->shippingMethodFactory->createNew();
        $shippingMethod->setCode($options['code']);
        $shippingMethod->setEnabled($options['enabled']);
        $shippingMethod->setZone($options['zone']);
        $shippingMethod->setCategory($options['category']);
        $shippingMethod->setCalculator($options['calculator']['type']);
        $shippingMethod->setConfiguration($options['calculator']['configuration']);

        foreach ($this->getLocales() as $localeCode) {
            $shippingMethod->setCurrentLocale($localeCode);
            $shippingMethod->setFallbackLocale($localeCode);

            $shippingMethod->setName(sprintf('[%s] %s', $localeCode, $options['name']));
        }

        return $shippingMethod;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode)
    {
        $resourceNode
            ->children()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->booleanNode('enabled')->end()
                ->scalarNode('zone')->cannotBeEmpty()->end()
                ->scalarNode('category')->cannotBeEmpty()->end()
                ->arrayNode('calculator')
                    ->children()
                        ->scalarNode('type')->isRequired()->cannotBeEmpty()->end()
                        ->variableNode('configuration')->end()
                    ->end()
                ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceOptionsResolver(array $options, OptionsResolver $optionsResolver)
    {
        $optionsResolver
            ->setRequired(['name'])
            ->setDefault('code', function (Options $options) {
                return StringInflector::nameToCode($options['name']);
            })
            ->setDefault('enabled', true)
            ->setAllowedTypes('enabled', 'bool')
            ->setDefault('zone', null)
            ->setAllowedTypes('zone', ['null', ZoneInterface::class])
            ->setNormalizer('zone', static::createResourceNormalizer($this->zoneRepository))
            ->setDefault('category', null)
            ->setAllowedTypes('category', ['null', ShippingCategoryInterface::class])
            ->setNormalizer('category', static::createResourceNormalizer($this->shippingCategoryRepository))
            ->setDefault('calculator', function (Options $options) {
                return ['type' => DefaultCalculators::FLAT_RATE, 'configuration' => ['amount' => 4200]];
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateResourcesOptions($amount)
    {
        $resourcesOptions = [];
        for ($i = 0; $i < (int) $amount; ++$i) {
            $resourcesOptions[] = ['name' => $this->faker->words(3, true)];
        }

        return $resourcesOptions;
    }

    /**
     * @return array
     */
    private function getLocales()
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }
}
