<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\Converter;

use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;

class CurrencyConverter implements CurrencyConverterInterface
{
    protected $exchangeRateRepository;
    private $cache;

    public function __construct(RepositoryInterface $exchangeRateRepository)
    {
        $this->exchangeRateRepository = $exchangeRateRepository;
    }

    public function convert($value, $currency)
    {
        $exchangeRate = $this->getExchangeRate($currency);

        if (null === $exchangeRate) {
            return $value;
        }

        return $value / $exchangeRate->getRate();
    }

    private function getExchangeRate($currency)
    {
        if (isset($this->cache[$currency])) {
            return $this->cache[$currency];
        }

        return $this->cache[$currency] = $this->exchangeRateRepository->findOneBy(array('currency' => $currency));
    }
}
