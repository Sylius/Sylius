<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Currency\Repository;

use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface ExchangeRateRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $firstCurrencyCode
     * @param string $secondCurrencyCode
     *
     * @return ExchangeRateInterface|null
     */
    public function findOneWithCurrencyPair($firstCurrencyCode, $secondCurrencyCode);
}
