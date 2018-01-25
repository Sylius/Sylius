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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;

final class LexicalContext implements Context
{
    /**
     * @Transform /^"(\-)?(?:€|£|￥|\$)((?:\d+\.)?\d+)"$/
     */
    public function getPriceFromString($sign, $price)
    {
        $this->validatePriceString($price);

        if ('-' === $sign) {
            $price *= -1;
        }

        return (int) round($price * 100, 2);
    }

    /**
     * @Transform /^"((?:\d+\.)?\d+)%"$/
     */
    public function getPercentageFromString($percentage)
    {
        return ((int) $percentage) / 100;
    }

    /**
     * @param string $price
     *
     * @throws \InvalidArgumentException
     */
    private function validatePriceString($price)
    {
        if (!(bool) preg_match('/^\d+(?:\.\d{1,2})?$/', $price)) {
            throw new \InvalidArgumentException('Price string should not have more than 2 decimal digits.');
        }
    }
}
