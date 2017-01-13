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
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Calculator\DefaultCalculators;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ShippingMethodExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
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
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

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
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(
        FactoryInterface $shippingMethodFactory,
        RepositoryInterface $zoneRepository,
        RepositoryInterface $shippingCategoryRepository,
        RepositoryInterface $localeRepository,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->shippingMethodFactory = $shippingMethodFactory;
        $this->zoneRepository = $zoneRepository;
        $this->shippingCategoryRepository = $shippingCategoryRepository;
        $this->localeRepository = $localeRepository;
        $this->channelRepository = $channelRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
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
        $shippingMethod->setCalculator($options['calculator']['type']);
        $shippingMethod->setConfiguration($options['calculator']['configuration']);
        $shippingMethod->setArchivedAt($options['archived_at']);

        if (array_key_exists('shipping_category', $options)) {
            $shippingMethod->setCategory($options['shipping_category']);
        }

        foreach ($this->getLocales() as $localeCode) {
            $shippingMethod->setCurrentLocale($localeCode);
            $shippingMethod->setFallbackLocale($localeCode);

            $shippingMethod->setName($options['name']);
            $shippingMethod->setDescription($options['description']);
        }

        foreach ($options['channels'] as $channel) {
            $shippingMethod->addChannel($channel);
        }

        return $shippingMethod;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('code', function (Options $options) {
                return StringInflector::nameToCode($options['name']);
            })
            ->setDefault('name', function (Options $options) {
                return $this->faker->words(3, true);
            })
            ->setDefault('description', function (Options $options) {
                return $this->faker->sentence();
            })
            ->setDefault('enabled', function (Options $options) {
                return $this->faker->boolean(90);
            })
            ->setAllowedTypes('enabled', 'bool')
            ->setDefault('zone', LazyOption::randomOne($this->zoneRepository))
            ->setAllowedTypes('zone', ['null', 'string', ZoneInterface::class])
            ->setNormalizer('zone', LazyOption::findOneBy($this->zoneRepository, 'code'))
            ->setDefined('shipping_category')
            ->setAllowedTypes('shipping_category', ['null', 'string', ShippingCategoryInterface::class])
            ->setNormalizer('shipping_category', LazyOption::findOneBy($this->shippingCategoryRepository, 'code'))
            ->setDefault('calculator', function (Options $options) {
                $configuration = [];
                /** @var ChannelInterface $channel */
                foreach ($options['channels'] as $channel) {
                    $configuration[$channel->getCode()] = ['amount' => $this->faker->randomNumber(4)];
                }

                return [
                    'type' => DefaultCalculators::FLAT_RATE,
                    'configuration' => $configuration,
                ];
            })
            ->setDefault('channels', LazyOption::all($this->channelRepository))
            ->setAllowedTypes('channels', 'array')
            ->setNormalizer('channels', LazyOption::findBy($this->channelRepository, 'code'))
            ->setDefault('archived_at', null)
            ->setAllowedTypes('archived_at', ['null', \DateTime::class])
        ;
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
