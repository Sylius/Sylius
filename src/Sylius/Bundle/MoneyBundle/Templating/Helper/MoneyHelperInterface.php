<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\Templating\Helper;

/**
 * @author Axel Vankrunkelsven <axel@digilabs.be>
 */
interface MoneyHelperInterface
{
    /**
     * @param int $amount
     * @param string $currencyCode
     * @param string|null $localeCode
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function formatAmount($amount, $currencyCode, $localeCode = null);
}
