<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Checker\Registry;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ExistingRuleCheckerException extends \InvalidArgumentException
{
    /**
     * @param string $type
     */
    public function __construct($type)
    {
        parent::__construct(sprintf('Shipping method rule checker of type "%s" already exist.', $type));
    }
}
