<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\ExchangeRate\Provider\Exception;
use Exception;

/**
 * Class CurrencyNotExistException
 *
 * Exception is raised when currency is not available in selected external service
 *
 * @package Sylius\Bundle\MoneyBundle\ExchangeRate\Provider\Exception
 */
class CurrencyNotExistException extends Exception
{

    /**
     * Create CurrencyNotExistException
     *
     * @param string    $currency
     * @param int       $code
     * @param Exception $previous
     */
    public function __construct($currency, $code = 0, Exception $previous = null)
    {
        $message = sprintf("Currency code %s don't exists.", $currency);
        parent::__construct($message, $code, $previous);
    }
}
