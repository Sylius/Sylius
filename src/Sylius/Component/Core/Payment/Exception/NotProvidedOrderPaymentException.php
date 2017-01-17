<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Payment\Exception;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class NotProvidedOrderPaymentException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Order payment could not be provided!');
    }
}
