<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\OrderProcessing\InventoryHandlerInterface;

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
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderInventoryListener');
    }
}
