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
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ChannelExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var ChannelFactoryInterface
     */
    private $channelFactory;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var RepositoryInterface
     */
    private $currencyRepository;

    /**
     * @var RepositoryInterface
     */
    private $zoneRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param ChannelFactoryInterface $channelFactory
     * @param RepositoryInterface $localeRepository
     * @param RepositoryInterface $currencyRepository
     * @param RepositoryInterface $zoneRepository
     */
    public function __construct(
        ChannelFactoryInterface $channelFactory,
        RepositoryInterface $localeRepository,
        RepositoryInterface $currencyRepository,
        RepositoryInterface $zoneRepository
    ) {
        $this->channelFactory = $channelFactory;
        $this->localeRepository = $localeRepository;
        $this->currencyRepository= $currencyRepository;
        $this->zoneRepository = $zoneRepository;

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

        /** @var ChannelInterface $channel */
        $channel = $this->channelFactory->createNamed($options['name']);
        $channel->setCode($options['code']);
        $channel->setHostname($options['hostname']);
        $channel->setEnabled($options['enabled']);
        $channel->setColor($options['color']);
        $channel->setDefaultTaxZone($options['default_tax_zone']);
        $channel->setTaxCalculationStrategy($options['tax_calculation_strategy']);
        $channel->setThemeName($options['theme_name']);
        $channel->setContactEmail($options['contact_email']);
        $channel->setSkippingShippingStepAllowed($options['skipping_shipping_step_allowed']);
        $channel->setSkippingPaymentStepAllowed($options['skipping_payment_step_allowed']);

        $channel->setDefaultLocale($options['default_locale']);
        foreach ($options['locales'] as $locale) {
            $channel->addLocale($locale);
        }

        $channel->setBaseCurrency($options['base_currency']);
        foreach ($options['currencies'] as $currency) {
            $channel->addCurrency($currency);
        }

        return $channel;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('name', function (Options $options) {
                return $this->faker->words(3, true);
            })
            ->setDefault('code', function (Options $options) {
                return StringInflector::nameToCode($options['name']);
            })
            ->setDefault('hostname', function (Options $options) {
                return $options['code'] . '.localhost';
            })
            ->setDefault('color', function (Options $options) {
                return $this->faker->colorName;
            })
            ->setDefault('enabled', function (Options $options) {
                return $this->faker->boolean(90);
            })
            ->setAllowedTypes('enabled', 'bool')
            ->setDefault('skipping_shipping_step_allowed', false)
            ->setAllowedTypes('skipping_shipping_step_allowed', 'bool')
            ->setDefault('skipping_payment_step_allowed', false)
            ->setAllowedTypes('skipping_payment_step_allowed', 'bool')
            ->setDefault('default_tax_zone', LazyOption::randomOne($this->zoneRepository))
            ->setAllowedTypes('default_tax_zone', ['null', 'string', ZoneInterface::class])
            ->setNormalizer('default_tax_zone', LazyOption::findOneBy($this->zoneRepository, 'code'))
            ->setDefault('tax_calculation_strategy', 'order_items_based')
            ->setAllowedTypes('tax_calculation_strategy', 'string')
            ->setDefault('default_locale', function (Options $options) {
                return $this->faker->randomElement($options['locales']);
            })
            ->setAllowedTypes('default_locale', ['string', LocaleInterface::class])
            ->setNormalizer('default_locale', LazyOption::findOneBy($this->localeRepository, 'code'))
            ->setDefault('locales', LazyOption::all($this->localeRepository))
            ->setAllowedTypes('locales', 'array')
            ->setNormalizer('locales', LazyOption::findBy($this->localeRepository, 'code'))
            ->setDefault('base_currency', function (Options $options) {
                return $this->faker->randomElement($options['currencies']);
            })
            ->setAllowedTypes('base_currency', ['string', CurrencyInterface::class])
            ->setNormalizer('base_currency', LazyOption::findOneBy($this->currencyRepository, 'code'))
            ->setDefault('currencies', LazyOption::all($this->currencyRepository))
            ->setAllowedTypes('currencies', 'array')
            ->setNormalizer('currencies', LazyOption::findBy($this->currencyRepository, 'code'))
            ->setDefault('theme_name', null)
            ->setDefault('contact_email', null)
        ;
    }
}
