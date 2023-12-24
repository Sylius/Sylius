<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\EventListener\Workflow\Order;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\Workflow\Marking;

final class AssignOrderTokenListenerSpec extends ObjectBehavior
{
    function let(OrderTokenAssignerInterface $orderTokenAssigner): void
    {
        $this->beConstructedWith($orderTokenAssigner);
    }

    function it_throws_an_exception_on_non_supported_subject(\stdClass $subject): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new TransitionEvent($subject->getWrappedObject(), new Marking())])
        ;
    }

    function it_assigns_order_token(OrderTokenAssignerInterface $orderTokenAssigner): void
    {
        $order = new Order();
        $event = new TransitionEvent($order, new Marking());

        $orderTokenAssigner->assignTokenValueIfNotSet($order)->shouldBeCalled();

        $this($event);
    }
}
