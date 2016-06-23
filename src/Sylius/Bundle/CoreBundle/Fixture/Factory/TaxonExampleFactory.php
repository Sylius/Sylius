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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TaxonExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $taxonFactory;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

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
        $this->taxonFactory = $taxonFactory;
        $this->localeRepository = $localeRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver =
            (new OptionsResolver())
                ->setDefault('name', function (Options $options) {
                    return $this->faker->words(3, true);
                })
                ->setDefault('code', function (Options $options) {
                    return StringInflector::nameToCode($options['name']);
                })
                ->setDefault('description', function (Options $options) {
                    return $this->faker->paragraph;
                })
                ->setDefault('parent', LazyOption::randomOneOrNull($taxonRepository, 70))
                ->setAllowedTypes('parent', ['null', 'string', TaxonInterface::class])
                ->setNormalizer('parent', function (Options $options, $previousValue) use ($taxonManager) {
                    $taxonManager->flush();

                    return $previousValue;
                })
                ->setNormalizer('parent', LazyOption::findOneBy($taxonRepository, 'code'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

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
