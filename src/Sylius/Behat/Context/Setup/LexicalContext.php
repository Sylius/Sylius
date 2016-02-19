<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class LexicalContext implements Context
{
    /**
     * @Transform /^"(?:€|£|\$)([^"]+)"$/
     */
    public function getPriceFromString($price)
    {
        return (int) round(($price * 100), 2);
    }

    /**
     * @Transform /^"([^"]+)%"$/
     */
    public function getPercentageFromString($percentage)
    {
        return ((int) $percentage)/100;
    }
}
