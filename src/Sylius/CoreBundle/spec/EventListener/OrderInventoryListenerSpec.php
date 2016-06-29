<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Core\OrderProcessing\InventoryHandlerInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderInventoryListenerSpec extends ObjectBehavior
{
    function let(InventoryHandlerInterface $inventoryHandler)
    {
        $this->beConstructedWith($inventoryHandler);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\CoreBundle\EventListener\OrderInventoryListener');
    }
}
