<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author  Pete Ward <peter.ward@reiss.com>
 * @author  Piotr Walków <walkowpiotr@gmail.com>
 */
class AdjustmentEvent extends GenericEvent
{
    // event thrown while adding adjustment on order level
    const ADJUSTMENT_ADDING_ORDER = 'sylius.adjustment.add.order';

    // event thrown while adding adjustment on inventory unit level
    const ADJUSTMENT_ADDING_INVENTORY_UNIT = 'sylius.adjustment.add.inventory_unit';
}