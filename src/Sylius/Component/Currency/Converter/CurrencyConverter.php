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

namespace Sylius\Component\Currency\Converter;

use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Sylius\Component\Currency\Repository\ExchangeRateRepositoryInterface;

final class CurrencyConverter implements CurrencyConverterInterface
{
    /**
     * @var ExchangeRateRepositoryInterface
     */
    private $exchangeRateRepository;

    /**
     * @var array|ExchangeRateInterface[]
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
    public function convert(int $amount, string $sourceCurrencyCode, string $targetCurrencyCode): int
    {
        if ($sourceCurrencyCode === $targetCurrencyCode) {
            return $amount;
        }

        $exchangeRate = $this->findExchangeRate($sourceCurrencyCode, $targetCurrencyCode);

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
     * @return ExchangeRateInterface|null
     */
    private function findExchangeRate(string $sourceCode, string $targetCode): ?ExchangeRateInterface
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
     * @param string $prefix
     * @param string $suffix
     *
     * @return string
     */
    private function createIndex(string $prefix, string $suffix): string
    {
        return sprintf('%s-%s', $prefix, $suffix);
    }
}
