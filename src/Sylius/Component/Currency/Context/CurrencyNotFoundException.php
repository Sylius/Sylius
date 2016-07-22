<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Currency\Context;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CurrencyNotFoundException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = null, \Exception $previousException = null)
    {
        parent::__construct($message ?: 'Currency could not be found!', 0, $previousException);
    }

    /**
     * @param string $currencyCode
     *
     * @return self
     */
    public static function notFound($currencyCode)
    {
        return new self(sprintf('Currency "%s" cannot be found!', $currencyCode));
    }

    /**
     * @param string $currencyCode
     *
     * @return self
     */
    public static function disabled($currencyCode)
    {
        return new self(sprintf('Currency "%s" is disabled!', $currencyCode));
    }

    /**
     * @param string $currencyCode
     * @param array $availableCurrenciesCodes
     *
     * @return self
     */
    public static function notAvailable($currencyCode, array $availableCurrenciesCodes)
    {
        return new self(sprintf(
            'Currency "%s" is not available! The available ones are: "%s".',
            $currencyCode,
            implode('", "', $availableCurrenciesCodes)
        ));
    }
}
