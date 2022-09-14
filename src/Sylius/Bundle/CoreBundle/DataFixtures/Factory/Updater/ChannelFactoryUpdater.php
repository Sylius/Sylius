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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\Updater;

use Sylius\Component\Core\Model\ChannelInterface;

final class ChannelFactoryUpdater implements ChannelFactoryUpdaterInterface
{
    public function update(ChannelInterface $channel, array $attributes): void
    {
        $channel->setCode($attributes['code']);
        $channel->setHostname($attributes['hostname']);
        $channel->setEnabled($attributes['enabled']);
        $channel->setColor($attributes['color']);
        $channel->setDefaultTaxZone($attributes['default_tax_zone']);
        $channel->setTaxCalculationStrategy($attributes['tax_calculation_strategy']);
        $channel->setThemeName($attributes['theme_name']);
        $channel->setContactEmail($attributes['contact_email']);
        $channel->setContactPhoneNumber($attributes['contact_phone_number']);
        $channel->setSkippingShippingStepAllowed($attributes['skipping_shipping_step_allowed']);
        $channel->setSkippingPaymentStepAllowed($attributes['skipping_payment_step_allowed']);
        $channel->setAccountVerificationRequired($attributes['account_verification_required']);
        $channel->setMenuTaxon($attributes['menu_taxon']);

        $channel->setDefaultLocale($attributes['default_locale']);
        foreach ($attributes['locales'] as $locale) {
            $channel->addLocale($locale);
        }

        $channel->setBaseCurrency($attributes['base_currency']);
        foreach ($attributes['currencies'] as $currency) {
            $channel->addCurrency($currency);
        }

        if (null !== $attributes['shop_billing_data']) {
            $channel->setShopBillingData($attributes['shop_billing_data']);
        }
    }
}
