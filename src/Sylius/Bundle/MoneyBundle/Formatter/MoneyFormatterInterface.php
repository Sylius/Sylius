<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sylius\Bundle\MoneyBundle\Formatter;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface MoneyFormatterInterface
{
    /**
     * @param int $amount
     * @param string $currencyCode
     * @param string $locale
     *
     * @return string
     */
    public function format($amount, $currencyCode, $locale = 'en');
}
