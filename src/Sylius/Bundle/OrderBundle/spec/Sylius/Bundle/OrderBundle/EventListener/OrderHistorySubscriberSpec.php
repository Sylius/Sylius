<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OrderBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
/**
 * @author Myke Hines <myke@webhines.com>
 */
class OrderHistorySubscriberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\EventListener\OrderHistorySubscriber');
    }

    /**
     * @param Doctrine\ORM\Event\PreUpdateEventArgs $args
     */
    function it_does_not_create_history_without_order_entity(PreUpdateEventArgs $args)
    {
        $args->getEntity()->shouldBeCalled()->willReturn($this);

        $this->preUpdate($args);

        $this->hasHistory()->shouldBe(false);
    }

    /**
     * @param Doctrine\ORM\Event\PreUpdateEventArgs $args
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface $order
     */
    function it_does_create_history_with_order_entity(PreUpdateEventArgs $args, OrderInterface $order)
    {
        $args->getEntity()->willReturn($order);
        $args->hasChangedField("state")->willReturn(true);
        $args->getNewValue("state")->willReturn("new");

        $order->getState()->willReturn("new");

        $this->preUpdate($args);

        $this->hasHistory()->shouldBe(true);
    }    
}
