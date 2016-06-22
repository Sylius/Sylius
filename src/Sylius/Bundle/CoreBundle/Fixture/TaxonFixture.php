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
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TaxonFixture extends AbstractResourceFixture
{
    /**
     * @var FactoryInterface
     */
    private $taxonFactory;

    /**
     * @var ObjectManager
     */
    private $taxonManager;

    /**
     * @var RepositoryInterface
     */
    private $taxonRepository;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @param FactoryInterface $taxonFactory
     * @param ObjectManager $taxonManager
     * @param RepositoryInterface $taxonRepository
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(
        FactoryInterface $taxonFactory,
        ObjectManager $taxonManager,
        RepositoryInterface $taxonRepository,
        RepositoryInterface $localeRepository
    ) {
        parent::__construct($taxonManager, 'taxons', 'name');

        $this->taxonFactory = $taxonFactory;
        $this->taxonManager = $taxonManager;
        $this->taxonRepository = $taxonRepository;
        $this->localeRepository = $localeRepository;

        $this->faker = \Faker\Factory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'taxon';
    }

    /**
     * {@inheritdoc}
     */
    protected function loadResource(array $options)
    {
        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonFactory->createNew();

        $taxon->setCode($options['code']);

        foreach ($this->getLocales() as $localeCode) {
            $taxon->setCurrentLocale($localeCode);
            $taxon->setFallbackLocale($localeCode);

            $taxon->setName(sprintf('[%s] %s', $localeCode, $options['name']));
            $taxon->setDescription(sprintf('[%s] %s', $localeCode, $options['description']));
        }

        $taxon->setParent($options['parent']);

        return $taxon;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode)
    {
        $resourceNode
            ->children()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->scalarNode('description')->cannotBeEmpty()->end()
                ->scalarNode('parent')->cannotBeEmpty()->end()
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
            ->setDefault('description', function (Options $options) {
                return $this->faker->paragraph;
            })
            ->setDefault('parent', null)
            ->setAllowedTypes('parent', ['null', 'string', TaxonInterface::class])
            ->setNormalizer('parent', function (Options $options, $parentCode) {
                // Ensure that there will be some root taxons as well
                if (mt_rand(1, 10) <= 3) {
                    return null;
                }

                $nestedNormalizer = static::createResourceNormalizer($this->taxonRepository);

                try {
                    $this->taxonManager->flush();

                    return $nestedNormalizer($options, $parentCode);
                } catch (\InvalidArgumentException $exception) {
                    if (null === $parentCode) {
                        return null;
                    }

                    throw $exception;
                }
            })
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
