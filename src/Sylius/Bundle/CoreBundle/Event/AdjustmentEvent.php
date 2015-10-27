<?php

namespace Sylius\Bundle\CoreBundle\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

class AdjustmentEvent extends GenericEvent
{
    // event thrown while adding adjustment on order level
    const ADJUSTMENT_ADDING_ORDER = 'sylius.adjustment.add.order';

    // event thrown while adding adjustment on inventory unit level
    const ADJUSTMENT_ADDING_INVENTORY_UNIT = 'sylius.adjustment.add.inventory_unit';
}