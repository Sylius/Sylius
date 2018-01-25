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

namespace Sylius\Bundle\CoreBundle\Installer\Setup;

use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

interface ChannelSetupInterface
{
    /**
     * @param LocaleInterface $locale
     * @param CurrencyInterface $currency
     */
    public function setup(LocaleInterface $locale, CurrencyInterface $currency): void;
}
