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

/**
 * Exception thrown when someone requests unavailable currency.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class UnavailableCurrencyException extends \InvalidArgumentException
{
    /**
     * @param string $currency
     */
    public function __construct($currency)
    {
        parent::__construct(sprintf('Currency "%s" is not available.', $currency));
    }
}
