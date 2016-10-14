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
final class ChannelExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var ChannelFactoryInterface
     */
    private $channelFactory;

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
     * @param RepositoryInterface $paymentMethodRepository
     * @param RepositoryInterface $shippingMethodRepository
     */
    public function __construct(
        ChannelFactoryInterface $channelFactory,
        RepositoryInterface $localeRepository,
        RepositoryInterface $currencyRepository,
        RepositoryInterface $paymentMethodRepository,
        RepositoryInterface $shippingMethodRepository
    ) {
        $this->channelFactory = $channelFactory;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver =
            (new OptionsResolver())
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
                ->setDefault('tax_calculation_strategy', 'order_items_based')
                ->setAllowedTypes('tax_calculation_strategy', 'string')
                ->setDefault('default_locale', function (Options $options) {
                    return $this->faker->randomElement($options['locales']);
                })
                ->setAllowedTypes('default_locale', ['string', LocaleInterface::class])
                ->setNormalizer('default_locale', LazyOption::findOneBy($localeRepository, 'code'))
                ->setDefault('locales', LazyOption::all($localeRepository))
                ->setAllowedTypes('locales', 'array')
                ->setNormalizer('locales', LazyOption::findBy($localeRepository, 'code'))
                ->setDefault('default_currency', function (Options $options) {
                    return $this->faker->randomElement($options['currencies']);
                })
                ->setAllowedTypes('default_currency', ['string', CurrencyInterface::class])
                ->setNormalizer('default_currency', LazyOption::findOneBy($currencyRepository, 'code'))
                ->setDefault('currencies', LazyOption::all($currencyRepository))
                ->setAllowedTypes('currencies', 'array')
                ->setNormalizer('currencies', LazyOption::findBy($currencyRepository, 'code'))
                ->setDefault('payment_methods', LazyOption::all($paymentMethodRepository))
                ->setAllowedTypes('payment_methods', 'array')
                ->setNormalizer('payment_methods', LazyOption::findBy($paymentMethodRepository, 'code'))
                ->setDefault('shipping_methods', LazyOption::all($shippingMethodRepository))
                ->setAllowedTypes('shipping_methods', 'array')
                ->setNormalizer('shipping_methods', LazyOption::findBy($shippingMethodRepository, 'code'))
                ->setDefault('theme_name', null)
        ;
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
        $channel->setTaxCalculationStrategy($options['tax_calculation_strategy']);
        $channel->setThemeName($options['theme_name']);

        $channel->setDefaultLocale($options['default_locale']);
        foreach ($options['locales'] as $locale) {
            $channel->addLocale($locale);
        }

        $channel->setDefaultCurrency($options['default_currency']);
        foreach ($options['currencies'] as $currency) {
            $channel->addCurrency($currency);
        }

        foreach ($options['payment_methods'] as $paymentMethod) {
            $channel->addPaymentMethod($paymentMethod);
        }

        foreach ($options['shipping_methods'] as $shippingMethod) {
            $channel->addShippingMethod($shippingMethod);
        }

        return $channel;
    }
}
