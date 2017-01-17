<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Currency\Converter;

use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Sylius\Component\Currency\Repository\ExchangeRateRepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class CurrencyConverter implements CurrencyConverterInterface
{
    /**
     * @var ExchangeRateRepositoryInterface
     */
    private $exchangeRateRepository;

    /**
     * @var array
     */
    private $cache;

    /**
     * @param ExchangeRateRepositoryInterface $exchangeRateRepository
     */
    public function __construct(ExchangeRateRepositoryInterface $exchangeRateRepository)
    {
        $this->exchangeRateRepository = $exchangeRateRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function convert($amount, $sourceCurrencyCode, $targetCurrencyCode)
    {
        if ($sourceCurrencyCode === $targetCurrencyCode) {
            return $amount;
        }

        $exchangeRate = $this->getExchangeRate($sourceCurrencyCode, $targetCurrencyCode);

        if (null === $exchangeRate) {
            return $amount;
        }

        if ($exchangeRate->getSourceCurrency()->getCode() === $sourceCurrencyCode) {
            return (int) round($amount * $exchangeRate->getRatio());
        }

        return (int) round($amount / $exchangeRate->getRatio());
    }

    /**
     * @param string $sourceCode
     * @param string $targetCode
     *
     * @return ExchangeRateInterface
     */
    private function getExchangeRate($sourceCode, $targetCode)
    {
        $sourceTargetIndex = $this->createIndex($sourceCode, $targetCode);

        if (isset($this->cache[$sourceTargetIndex])) {
            return $this->cache[$sourceTargetIndex];
        }

        $targetSourceIndex = $this->createIndex($targetCode, $sourceCode);

        if (isset($this->cache[$targetSourceIndex])) {
            return $this->cache[$targetSourceIndex];
        }

        return $this->cache[$sourceTargetIndex] = $this->exchangeRateRepository->findOneWithCurrencyPair($sourceCode, $targetCode);
    }

    /**
     * @param $prefix
     * @param $suffix
     *
     * @return string
     */
    private function createIndex($prefix, $suffix)
    {
        return sprintf('%s-%s', $prefix, $suffix);
    }
}
