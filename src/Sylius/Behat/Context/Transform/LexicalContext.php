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

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
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
        if (strlen(substr(strrchr($price, '.'), 1)) > 2) {
            throw new \InvalidArgumentException('Price string should not have more than 2 decimal digits.');
        }
    }
}
