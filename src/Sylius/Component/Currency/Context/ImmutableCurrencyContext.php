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
final class ImmutableCurrencyContext implements CurrencyContextInterface
{
    /**
     * @var string
     */
    private $currencyCode;

    /**
     * @param string $currencyCode
     */
    public function __construct($currencyCode)
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }
}
