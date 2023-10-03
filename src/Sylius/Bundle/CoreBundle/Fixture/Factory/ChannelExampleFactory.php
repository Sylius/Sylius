<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Faker\Factory;
use Faker\Generator;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Addressing\Model\Scope as AddressingScope;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\Scope;
use Sylius\Component\Core\Model\ShopBillingData;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChannelExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    private Generator $faker;

    private OptionsResolver $optionsResolver;

    private ?TaxonRepositoryInterface $taxonRepository;

    private ?FactoryInterface $shopBillingDataFactory;

    public function __construct(
        private ChannelFactoryInterface $channelFactory,
        private RepositoryInterface $localeRepository,
        private RepositoryInterface $currencyRepository,
        private RepositoryInterface $zoneRepository,
        ?TaxonRepositoryInterface $taxonRepository = null,
        ?FactoryInterface $shopBillingDataFactory = null,
    ) {
        if (null === $taxonRepository) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.8',
                'Passing a $taxonRepository as the fifth argument is deprecated and will be prohibited in Sylius 2.0',
            );
        }

        if (null === $shopBillingDataFactory) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.8',
                'Passing a $shopBillingDataFactory as the sixth argument is deprecated and will be prohibited in Sylius 2.0',
            );
        }
        $this->taxonRepository = $taxonRepository;
        $this->shopBillingDataFactory = $shopBillingDataFactory;

        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): ChannelInterface
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
        $channel->setContactPhoneNumber($options['contact_phone_number']);
        $channel->setSkippingShippingStepAllowed($options['skipping_shipping_step_allowed']);
        $channel->setSkippingPaymentStepAllowed($options['skipping_payment_step_allowed']);
        $channel->setAccountVerificationRequired($options['account_verification_required']);
        $channel->setShippingAddressInCheckoutRequired($options['shipping_address_in_checkout_required']);

        if (null !== $this->taxonRepository) {
            $channel->setMenuTaxon($options['menu_taxon']);
        }

        $channel->setDefaultLocale($options['default_locale']);
        foreach ($options['locales'] as $locale) {
            $channel->addLocale($locale);
        }

        $channel->setBaseCurrency($options['base_currency']);
        foreach ($options['currencies'] as $currency) {
            $channel->addCurrency($currency);
        }

        if (isset($options['shop_billing_data']) && null !== $options['shop_billing_data']) {
            $shopBillingData = $this->shopBillingDataFactory ? $this->shopBillingDataFactory->createNew() : new ShopBillingData();
            $shopBillingData->setCompany($options['shop_billing_data']['company'] ?? null);
            $shopBillingData->setTaxId($options['shop_billing_data']['tax_id'] ?? null);
            $shopBillingData->setCountryCode($options['shop_billing_data']['country_code'] ?? null);
            $shopBillingData->setStreet($options['shop_billing_data']['street'] ?? null);
            $shopBillingData->setCity($options['shop_billing_data']['city'] ?? null);
            $shopBillingData->setPostcode($options['shop_billing_data']['postcode'] ?? null);

            $channel->setShopBillingData($shopBillingData);
        }

        return $channel;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('name', function (Options $options): string {
                /** @var string $words */
                $words = $this->faker->words(3, true);

                return $words;
            })
            ->setDefault('code', fn (Options $options): string => StringInflector::nameToCode($options['name']))
            ->setDefault('hostname', fn (Options $options): string => $options['code'] . '.localhost')
            ->setDefault('color', fn (Options $options): string => $this->faker->hexColor)
            ->setDefault('enabled', fn (Options $options): bool => $this->faker->boolean(90))
            ->setAllowedTypes('enabled', 'bool')
            ->setDefault('skipping_shipping_step_allowed', false)
            ->setAllowedTypes('skipping_shipping_step_allowed', 'bool')
            ->setDefault('skipping_payment_step_allowed', false)
            ->setAllowedTypes('skipping_payment_step_allowed', 'bool')
            ->setDefault('account_verification_required', true)
            ->setAllowedTypes('account_verification_required', 'bool')
            ->setDefault('shipping_address_in_checkout_required', false)
            ->setAllowedTypes('shipping_address_in_checkout_required', 'bool')
            ->setDefault(
                'default_tax_zone',
                LazyOption::randomOneOrNull($this->zoneRepository, 100, ['scope' => [Scope::TAX, AddressingScope::ALL]]),
            )
            ->setAllowedTypes('default_tax_zone', ['null', 'string', ZoneInterface::class])
            ->setNormalizer(
                'default_tax_zone',
                LazyOption::findOneBy($this->zoneRepository, 'code', ['scope' => [Scope::TAX, AddressingScope::ALL]]),
            )
            ->setDefault('tax_calculation_strategy', 'order_items_based')
            ->setAllowedTypes('tax_calculation_strategy', 'string')
            ->setDefault('default_locale', fn (Options $options): LocaleInterface => $this->faker->randomElement($options['locales']))
            ->setAllowedTypes('default_locale', ['string', LocaleInterface::class])
            ->setNormalizer('default_locale', LazyOption::getOneBy($this->localeRepository, 'code'))
            ->setDefault('locales', LazyOption::all($this->localeRepository))
            ->setAllowedTypes('locales', 'array')
            ->setNormalizer('locales', LazyOption::findBy($this->localeRepository, 'code'))
            ->setDefault('base_currency', fn (Options $options): CurrencyInterface => $this->faker->randomElement($options['currencies']))
            ->setAllowedTypes('base_currency', ['string', CurrencyInterface::class])
            ->setNormalizer('base_currency', LazyOption::getOneBy($this->currencyRepository, 'code'))
            ->setDefault('currencies', LazyOption::all($this->currencyRepository))
            ->setAllowedTypes('currencies', 'array')
            ->setNormalizer('currencies', LazyOption::findBy($this->currencyRepository, 'code'))
            ->setDefault('theme_name', null)
            ->setDefault('contact_email', null)
            ->setDefault('contact_phone_number', null)
            ->setDefault('shop_billing_data', null)
        ;

        if (null !== $this->taxonRepository) {
            $resolver
                ->setDefault('menu_taxon', LazyOption::randomOneOrNull($this->taxonRepository))
                ->setAllowedTypes('menu_taxon', ['null', 'string', TaxonInterface::class])
                ->setNormalizer('menu_taxon', LazyOption::findOneBy($this->taxonRepository, 'code'))
            ;
        }
    }
}
