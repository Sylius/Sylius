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

namespace Sylius\Component\Currency\Repository;

use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ExchangeRateRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $firstCurrencyCode
     * @param string $secondCurrencyCode
     *
     * @return ExchangeRateInterface|null
     */
    public function findOneWithCurrencyPair(string $firstCurrencyCode, string $secondCurrencyCode): ?ExchangeRateInterface;
}
