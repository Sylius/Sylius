<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Action\Registry;

/**
 * This exception should be thrown by promotion action registry
 * when action of given type does not exist.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class NonExistingPromotionActionException extends \InvalidArgumentException
{
    public function __construct($type)
    {
        parent::__construct(sprintf('Promotion action of type "%s" does not exist.', $type));
    }
}
