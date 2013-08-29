<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Checker\Registry;

/**
 * This exception should be thrown by rule checker registry
 * when checker of given type already exists.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ExistingRuleCheckerException extends \InvalidArgumentException
{
    public function __construct($type)
    {
        parent::__construct(sprintf('Shipping method rule checker of type "%s" already exist.', $type));
    }
}
