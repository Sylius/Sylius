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
use Sylius\Component\Attribute\Factory\AttributeFactoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Model\OptionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProductAttributeFixture extends AbstractResourceFixture
{
    /**
     * @var AttributeFactoryInterface
     */
    private $productAttributeFactory;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var array
     */
    private $attributeTypes;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @param AttributeFactoryInterface $productAttributeFactory
     * @param ObjectManager $productAttributeManager
     * @param RepositoryInterface $localeRepository
     * @param array $attributeTypes
     */
    public function __construct(
        AttributeFactoryInterface $productAttributeFactory,
        ObjectManager $productAttributeManager,
        RepositoryInterface $localeRepository,
        array $attributeTypes
    ) {
        parent::__construct($productAttributeManager, 'product_attributes', 'name');

        $this->productAttributeFactory = $productAttributeFactory;
        $this->localeRepository = $localeRepository;
        $this->attributeTypes = array_keys($attributeTypes);

        $this->faker = \Faker\Factory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'product_attribute';
    }

    /**
     * {@inheritdoc}
     */
    protected function loadResource(array $options)
    {
        /** @var OptionInterface $productAttribute */
        $productAttribute = $this->productAttributeFactory->createTyped($options['type']);
        $productAttribute->setCode($options['code']);

        foreach ($this->getLocales() as $localeCode) {
            $productAttribute->setCurrentLocale($localeCode);
            $productAttribute->setFallbackLocale($localeCode);

            $productAttribute->setName(sprintf('[%s] %s', $localeCode, $options['name']));
        }

        return $productAttribute;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode)
    {
        $resourceNode
            ->children()
                ->enumNode('type')->values($this->attributeTypes)->cannotBeEmpty()->end()
                ->scalarNode('code')->cannotBeEmpty()->end()
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
            ->setDefault('type', function (Options $options) {
                return $this->faker->randomElement($this->attributeTypes);
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
