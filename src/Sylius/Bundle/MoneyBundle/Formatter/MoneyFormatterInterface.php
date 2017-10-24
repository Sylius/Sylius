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

namespace Sylius\Bundle\MoneyBundle\Formatter;

interface MoneyFormatterInterface
{
    /**
     * @param int $amount
     * @param string $currencyCode
     * @param string|null $locale
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function format(int $amount, string $currencyCode, ?string $locale = null): string;
}
