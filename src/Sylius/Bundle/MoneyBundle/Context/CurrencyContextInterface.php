<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\Context;

interface CurrencyContextInterface
{
    public function getDefaultCurrency();
    public function getCurrency();
    public function setCurrency($currency);
}
