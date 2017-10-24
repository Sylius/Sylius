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

namespace Sylius\Bundle\MoneyBundle\Templating\Helper;

interface FormatMoneyHelperInterface
{
    /**
     * @param int $amount
     * @param string $currencyCode
     * @param string $localeCode
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function formatAmount(int $amount, string $currencyCode, string $localeCode): string;
}
