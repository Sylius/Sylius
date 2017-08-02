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

namespace Sylius\Bundle\MoneyBundle\Templating\Helper;

/**
 * @author Axel Vankrunkelsven <axel@digilabs.be>
 */
interface ConvertMoneyHelperInterface
{
    /**
     * @param int $amount
     * @param string|null $sourceCurrencyCode
     * @param string|null $targetCurrencyCode
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function convertAmount($amount, $sourceCurrencyCode, $targetCurrencyCode);
}
