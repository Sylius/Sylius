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
use Sylius\Bundle\OrderBundle\Generator\OrderNumberGeneratorInterface;
use Sylius\Bundle\OrderBundle\Repository\NumberRepositoryInterface;
use Sylius\Bundle\OrderBundle\Model\OrderInterface;
use Sylius\Bundle\OrderBundle\Model\NumberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class OrderNumberListenerSpec extends ObjectBehavior
{
    public function let(OrderNumberGeneratorInterface $generator, NumberRepositoryInterface $numberRepository, ObjectManager $numberManager)
    {
        $this->beConstructedWith($generator, $numberRepository, $numberManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\EventListener\OrderNumberListener');
    }

    function it_generates_order_number($generator, $numberRepository, GenericEvent $event, OrderInterface $order, NumberInterface $number)
    {
        $event->getSubject()->willReturn($order);

        $numberRepository->createNew()->shouldBeCalled()->willReturn($number);
        $number->setOrder($order)->shouldBeCalled();

        $generator->generate($order)->shouldBeCalled();

        $this->generateOrderNumber($event);
    }
}
