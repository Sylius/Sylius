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

namespace Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues;

use Faker\Generator;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CurrencyFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxonFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneFactoryInterface;
use Sylius\Component\Addressing\Model\Scope as AddressingScope;
use Sylius\Component\Core\Model\Scope;

final class ChannelDefaultValues implements ChannelDefaultValuesInterface
{
    public function __construct(
        private ZoneFactoryInterface $zoneFactory,
        private LocaleFactoryInterface $localeFactory,
        private CurrencyFactoryInterface $currencyFactory,
        private TaxonFactoryInterface $taxonFactory,
    ) {
    }

    public function getDefaults(Generator $faker): array
    {
        return [
            'name' => $faker->words(3, true),
            'code' => null,
            'hostname' => null,
            'color' => $faker->colorName(),
            'enabled' => $faker->boolean(90),
            'skipping_shipping_step_allowed' => false,
            'skipping_payment_step_allowed' => false,
            'account_verification_required' => true,
            'default_tax_zone' => $this->zoneFactory::randomOrCreate(['scope' => $faker->boolean() ? Scope::TAX : AddressingScope::ALL]),
            'tax_calculation_strategy' => 'order_items_based',
            'default_locale' => $this->localeFactory::randomOrCreate(),
            'locales' => $this->localeFactory::all(),
            'base_currency' => $this->currencyFactory::randomOrCreate(),
            'currencies' => $this->currencyFactory::all(),
            'theme_name' => null,
            'contact_email' => null,
            'contact_phone_number' => null,
            'shop_billing_data' => null,
            'menu_taxon' => $this->taxonFactory::randomOrCreate(),
        ];
    }
}
