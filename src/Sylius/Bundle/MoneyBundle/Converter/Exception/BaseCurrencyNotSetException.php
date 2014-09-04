<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\Converter\Exception;

use Exception;

/**
 * Class BaseCurrencyNotSetException
 *
 * This exception will be thrown when base currency is not set
 * Base currency is currency with 1.000 rate value
 *
 * @author Ivan Đurđevac <djurdjevac@gmail.com>
 */
class BaseCurrencyNotSetException extends Exception
{
    /**
     * Set general message in constructor
     *
     * @param string    $message
     * @param int       $code
     * @param Exception $previous
     */
    public function __construct($message = "Base currency with rate value 1.000 don't exists in database", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
