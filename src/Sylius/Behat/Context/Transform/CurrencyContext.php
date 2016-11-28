<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Currency\Converter\CurrencyNameConverterInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class CurrencyContext implements Context
{
    /**
     * @var CurrencyNameConverterInterface
     */
    private $currencyNameConverter;

    /**
     * @var RepositoryInterface
     */
    private $currencyRepository;

    /**
     * @param CurrencyNameConverterInterface $currencyNameConverter
     * @param RepositoryInterface $currencyRepository
     */
    public function __construct(
        CurrencyNameConverterInterface $currencyNameConverter,
        RepositoryInterface $currencyRepository
    ) {
        $this->currencyNameConverter = $currencyNameConverter;
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * @Transform :currency
     * @Transform :sourceCurrency
     * @Transform :targetCurrency
     * @Transform /^currency "([^"]+)"$/
     * @Transform /^"([^"]+)" currency$/
     */
    public function getCurrencyByName($currencyName)
    {
        $currency = $this->currencyRepository->findOneBy(['code' => $this->getCurrencyCodeByName($currencyName)]);
        Assert::notNull(
            $currency,
            sprintf('Currency with name %s does not exist.', $currencyName)
        );

        return $currency;
    }

    /**
     * @Transform :currencyCode
     * @Transform :secondCurrencyCode
     * @Transform :thirdCurrencyCode
     */
    public function getCurrencyCodeByName($currencyName)
    {
        // If it's already a currency code - just return it.
        if (strlen($currencyName) === 3 && strtoupper($currencyName) === $currencyName) {
            return $currencyName;
        }

        return $this->currencyNameConverter->convertToCode($currencyName);
    }
}
