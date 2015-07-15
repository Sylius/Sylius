<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Currency\Model\CurrenciesAwareInterface;
use Sylius\Component\Locale\Model\LocalesAwareInterface;
use Sylius\Component\Payment\Model\PaymentMethodsAwareInterface;
use Sylius\Component\Shipping\Model\ShippingMethodsAwareInterface;
use Sylius\Component\Taxonomy\Model\TaxonomiesAwareInterface;

/**
 * Sylius core channel interface.
 *
 * Model implementing this interface should reference several:
 *   - Currencies;
 *   - Locales;
 *   - Payment methods;
 *   - Shipping methods;
 *   - Taxonomies.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ChannelInterface extends
    BaseChannelInterface,
    CurrenciesAwareInterface,
    LocalesAwareInterface,
    PaymentMethodsAwareInterface,
    ShippingMethodsAwareInterface,
    TaxonomiesAwareInterface
{
}
