<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Action\Registry;

/**
 * This exception should be thrown by promotion action registry
 * when action of given type already exists.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ExistingPromotionActionException extends \InvalidArgumentException
{
    public function __construct($type)
    {
        parent::__construct(sprintf('Promotion action of type "%s" already exist.', $type));
    }
}
