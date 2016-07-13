<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OrderBundle\NumberGenerator;

use Doctrine\ORM\EntityManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\NumberGenerator\OrderNumberGenerator;
use Sylius\Bundle\OrderBundle\NumberGenerator\OrderNumberGeneratorInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderSequenceInterface;
use Sylius\Component\Resource\Factory\Factory;

/**
 * @mixin OrderNumberGenerator
 *
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class OrderNumberGeneratorSpec extends ObjectBehavior
{
    function let(
        EntityRepository $sequenceRepository,
        Factory $sequenceFactory,
        EntityManager $entityManager
    ) {
        $this->beConstructedWith($sequenceRepository, $sequenceFactory, $entityManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\NumberGenerator\OrderNumberGenerator');
    }

    function it_implements_order_number_generator_interface()
    {
        $this->shouldImplement(OrderNumberGeneratorInterface::class);
    }


    function it_generates_order_number(
        EntityRepository $sequenceRepository,
        OrderInterface $order,
        OrderSequenceInterface $sequence
    ) {
        $sequence->getIndex()->willReturn(6);
        $sequenceRepository->findOneBy([])->willReturn($sequence);

        $order->getNumber()->willReturn(null);

        $order->setNumber('00000007')->shouldBeCalled();
        $sequence->incrementIndex()->shouldBeCalled();

        $this->generate($order);
    }
}
