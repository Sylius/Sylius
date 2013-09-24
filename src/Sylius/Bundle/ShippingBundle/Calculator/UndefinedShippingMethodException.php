<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Calculator;

/**
 * This exception should be thrown by calculator when given
 * shipment does not have a shipping method defined.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class UndefinedShippingMethodException extends \InvalidArgumentException
{
}
