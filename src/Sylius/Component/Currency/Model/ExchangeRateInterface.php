<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Currency\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface ExchangeRateInterface extends ResourceInterface
{
    public function getRatio(): ?float;

    public function setRatio(?float $ratio);

    public function getSourceCurrency(): ?CurrencyInterface;

    public function setSourceCurrency(CurrencyInterface $currency): void;

    public function getTargetCurrency(): ?CurrencyInterface;

    public function setTargetCurrency(CurrencyInterface $currency): void;
}
