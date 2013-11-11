<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Money\Converter;

use Sylius\Component\Resource\Repository\RepositoryInterface;

class CurrencyConverter implements CurrencyConverterInterface
{
    protected $exchangeRateRepository;

    public function __construct(RepositoryInterface $exchangeRateRepository)
    {
        $this->exchangeRateRepository = $exchangeRateRepository;
    }

    public function convert($value, $currency)
    {
        if (null === $exchangeRate = $this->exchangeRateRepository->findOneBy(array('currency' => $currency))) {
            return $value;
        }

        return $value / $exchangeRate->getRate();
    }
}
