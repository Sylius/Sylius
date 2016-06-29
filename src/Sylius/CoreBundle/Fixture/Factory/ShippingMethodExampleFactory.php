<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\CoreBundle\Fixture\Factory;

use Sylius\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Addressing\Model\ZoneInterface;
use Sylius\Core\Formatter\StringInflector;
use Sylius\Core\Model\ShippingMethodInterface;
use Sylius\Locale\Model\LocaleInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Sylius\Resource\Repository\RepositoryInterface;
use Sylius\Shipping\Calculator\DefaultCalculators;
use Sylius\Shipping\Model\ShippingCategoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ShippingMethodExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $shippingMethodFactory;

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
     * @param FactoryInterface $shippingMethodFactory
     * @param RepositoryInterface $zoneRepository
     * @param RepositoryInterface $shippingCategoryRepository
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(
        FactoryInterface $shippingMethodFactory,
        RepositoryInterface $zoneRepository,
        RepositoryInterface $shippingCategoryRepository,
        RepositoryInterface $localeRepository
    ) {
        $this->shippingMethodFactory = $shippingMethodFactory;
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
                ->setDefault('enabled', function (Options $options) {
                    return $this->faker->boolean(90);
                })
                ->setAllowedTypes('enabled', 'bool')
                ->setDefault('zone', LazyOption::randomOne($zoneRepository))
                ->setAllowedTypes('zone', ['null', 'string', ZoneInterface::class])
                ->setNormalizer('zone', LazyOption::findOneBy($zoneRepository, 'code'))
                ->setDefault('shipping_category', LazyOption::randomOne($shippingCategoryRepository))
                ->setAllowedTypes('shipping_category', ['null', 'string', ShippingCategoryInterface::class])
                ->setNormalizer('shipping_category', LazyOption::findOneBy($shippingCategoryRepository, 'code'))
                ->setDefault('calculator', function (Options $options) {
                    return ['type' => DefaultCalculators::FLAT_RATE, 'configuration' => ['amount' => 4200]];
                })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $this->shippingMethodFactory->createNew();
        $shippingMethod->setCode($options['code']);
        $shippingMethod->setEnabled($options['enabled']);
        $shippingMethod->setZone($options['zone']);
        $shippingMethod->setCategory($options['shipping_category']);
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
