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
use Sylius\Component\Product\Model\OptionInterface;
use Sylius\Component\Product\Model\OptionValueInterface;
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
final class ProductOptionFixture extends AbstractResourceFixture
{
    /**
     * @var FactoryInterface
     */
    private $productOptionFactory;

    /**
     * @var FactoryInterface
     */
    private $productOptionValueFactory;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @param FactoryInterface $productOptionFactory
     * @param ObjectManager $productOptionManager
     * @param FactoryInterface $productOptionValueFactory
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(
        FactoryInterface $productOptionFactory,
        ObjectManager $productOptionManager,
        FactoryInterface $productOptionValueFactory,
        RepositoryInterface $localeRepository
    ) {
        parent::__construct($productOptionManager, 'product_options', 'name');

        $this->productOptionFactory = $productOptionFactory;
        $this->productOptionValueFactory = $productOptionValueFactory;
        $this->localeRepository = $localeRepository;

        $this->faker = \Faker\Factory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'product_option';
    }

    /**
     * {@inheritdoc}
     */
    protected function loadResource(array $options)
    {
        /** @var OptionInterface $productOption */
        $productOption = $this->productOptionFactory->createNew();
        $productOption->setCode($options['code']);

        foreach ($this->getLocales() as $localeCode) {
            $productOption->setCurrentLocale($localeCode);
            $productOption->setFallbackLocale($localeCode);

            $productOption->setName(sprintf('[%s] %s', $localeCode, $options['name']));
        }

        foreach ($options['values'] as $code => $value) {
            /** @var OptionValueInterface $productOptionValue */
            $productOptionValue = $this->productOptionValueFactory->createNew();
            $productOptionValue->setCode($code);

            foreach ($this->getLocales() as $localeCode) {
                $productOptionValue->setCurrentLocale($localeCode);
                $productOptionValue->setFallbackLocale($localeCode);

                $productOptionValue->setValue(sprintf('[%s] %s', $localeCode, $value));

            }

            $productOption->addValue($productOptionValue);
        }

        return $productOption;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode)
    {
        $resourceNode
            ->children()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->arrayNode('values')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('code')
                    ->prototype('scalar')
                ->end()
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
            ->setDefault('values', [])
            ->setAllowedTypes('values', 'array')
            ->setNormalizer('values', function (Options $options, $values) {
                if (!empty($values)) {
                    return $values;
                }

                for ($i = 1; $i <= 5; ++$i) {
                    $values[sprintf('%s-option#%d', $options['code'], $i)] = sprintf('%s #i%d', $options['name'], $i);
                }

                return $values;
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
