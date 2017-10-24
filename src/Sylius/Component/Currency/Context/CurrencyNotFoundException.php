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

namespace Sylius\Component\Currency\Context;

final class CurrencyNotFoundException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct(?string $message = null, ?\Exception $previousException = null)
    {
        parent::__construct($message ?: 'Currency could not be found!', 0, $previousException);
    }

    /**
     * @param string $currencyCode
     *
     * @return self
     */
    public static function notFound(string $currencyCode): self
    {
        return new self(sprintf('Currency "%s" cannot be found!', $currencyCode));
    }

    /**
     * @param string $currencyCode
     *
     * @return self
     */
    public static function disabled(string $currencyCode): self
    {
        return new self(sprintf('Currency "%s" is disabled!', $currencyCode));
    }

    /**
     * @param string $currencyCode
     * @param array|string[] $availableCurrenciesCodes
     *
     * @return self
     */
    public static function notAvailable(string $currencyCode, array $availableCurrenciesCodes): self
    {
        return new self(sprintf(
            'Currency "%s" is not available! The available ones are: "%s".',
            $currencyCode,
            implode('", "', $availableCurrenciesCodes)
        ));
    }
}
