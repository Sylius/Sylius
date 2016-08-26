<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CurrencyBundle\Templating\Helper;

/**
 * @author Axel Vankrunkelsven <axel@digilabs.be>
 */
interface CurrencyHelperInterface
{
    /**
     * @param string $code
     *
     * @return string
     */
    public function convertCurrencyCodeToSymbol($code);
}
