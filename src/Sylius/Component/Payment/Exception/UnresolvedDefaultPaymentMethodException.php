<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Payment\Exception;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class UnresolvedDefaultPaymentMethodException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Default payment method could not be resolved!');
    }
}
