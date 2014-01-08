<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Promotion\Checker;

use InvalidArgumentException;

class OrderInterfaceNotImplementedException extends InvalidArgumentException
{
    public function __construct($subject)
    {
        parent::__construct(sprintf(
            '%s does not implement OrderInterface.',
            get_class($subject)
        ));
    }
}
