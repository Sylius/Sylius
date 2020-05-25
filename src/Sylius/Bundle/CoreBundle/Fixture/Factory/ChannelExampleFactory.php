<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

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
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChannelExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var ChannelFactoryInterface */
    private $channelFactory;

    /** @var RepositoryInterface */
    private $localeRepository;

    /** @var RepositoryInterface */
    private $currencyRepository;

    /** @var RepositoryInterface */
    private $zoneRepository;

    /** @var \Faker\Generator */
    private $faker;

    /** @var OptionsResolver */
    private $optionsResolver;

    /** @var TaxonRepositoryInterface|null */
    private $taxonRepository;

    public function __construct(
        ChannelFactoryInterface $channelFactory,
        RepositoryInterface $localeRepository,
        RepositoryInterface $currencyRepository,
        RepositoryInterface $zoneRepository,
        ?TaxonRepositoryInterface $taxonRepository = null
    ) {
        if (null === $taxonRepository) {
            @trigger_error('Passing RouterInterface as the fourth argument is deprecated since 1.8 and will be prohibited in 2.0', \E_USER_DEPRECATED);
        }

        $this->channelFactory = $channelFactory;
        $this->localeRepository = $localeRepository;
        $this->currencyRepository = $currencyRepository;
        $this->zoneRepository = $zoneRepository;
        $this->taxonRepository = $taxonRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
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
        $channel->setSkippingShippingStepAllowed($options['skipping_shipping_step_allowed']);
        $channel->setSkippingPaymentStepAllowed($options['skipping_payment_step_allowed']);
        $channel->setAccountVerificationRequired($options['account_verification_required']);

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
            $shopBillingData = new ShopBillingData();
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

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('name', function (Options $options): string {
                /** @var string $words */
                $words = $this->faker->words(3, true);

                return $words;
            })
            ->setDefault('code', function (Options $options): string {
                return StringInflector::nameToCode($options['name']);
            })
            ->setDefault('hostname', function (Options $options): string {
                return $options['code'] . '.localhost';
            })
            ->setDefault('color', function (Options $options): string {
                return (string) $this->faker->colorName;
            })
            ->setDefault('enabled', function (Options $options): bool {
                return (bool) $this->faker->boolean(90);
            })
            ->setAllowedTypes('enabled', 'bool')
            ->setDefault('skipping_shipping_step_allowed', false)
            ->setAllowedTypes('skipping_shipping_step_allowed', 'bool')
            ->setDefault('skipping_payment_step_allowed', false)
            ->setAllowedTypes('skipping_payment_step_allowed', 'bool')
            ->setDefault('account_verification_required', true)
            ->setAllowedTypes('account_verification_required', 'bool')
            ->setDefault(
                'default_tax_zone',
                LazyOption::randomOneOrNull($this->zoneRepository, 100, ['scope' => [Scope::TAX, AddressingScope::ALL]])
            )
            ->setAllowedTypes('default_tax_zone', ['null', 'string', ZoneInterface::class])
            ->setNormalizer(
                'default_tax_zone',
                LazyOption::findOneBy($this->zoneRepository, 'code', ['scope' => [Scope::TAX, AddressingScope::ALL]])
            )
            ->setDefault('tax_calculation_strategy', 'order_items_based')
            ->setAllowedTypes('tax_calculation_strategy', 'string')
            ->setDefault('default_locale', function (Options $options): LocaleInterface {
                return $this->faker->randomElement($options['locales']);
            })
            ->setAllowedTypes('default_locale', ['string', LocaleInterface::class])
            ->setNormalizer('default_locale', LazyOption::findOneBy($this->localeRepository, 'code'))
            ->setDefault('locales', LazyOption::all($this->localeRepository))
            ->setAllowedTypes('locales', 'array')
            ->setNormalizer('locales', LazyOption::findBy($this->localeRepository, 'code'))
            ->setDefault('base_currency', function (Options $options): CurrencyInterface {
                return $this->faker->randomElement($options['currencies']);
            })
            ->setAllowedTypes('base_currency', ['string', CurrencyInterface::class])
            ->setNormalizer('base_currency', LazyOption::findOneBy($this->currencyRepository, 'code'))
            ->setDefault('currencies', LazyOption::all($this->currencyRepository))
            ->setAllowedTypes('currencies', 'array')
            ->setNormalizer('currencies', LazyOption::findBy($this->currencyRepository, 'code'))
            ->setDefault('theme_name', null)
            ->setDefault('contact_email', null)
            ->setDefault('shop_billing_data', null)
        ;

        if (null !== $this->taxonRepository) {
            $resolver
                ->setDefault('menu_taxon', LazyOption::randomOne($this->taxonRepository))
                ->setAllowedTypes('menu_taxon', ['null', 'string', TaxonInterface::class])
                ->setNormalizer('menu_taxon', LazyOption::findOneBy($this->taxonRepository, 'code'))
            ;
        }
    }
}
