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

use Sylius\Component\Currency\Model\CurrencyInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CurrencyContextInterface
{
    /**
     * @return CurrencyInterface
     *
     * @throws CurrencyNotFoundException
     */
    public function getCurrency();
}
