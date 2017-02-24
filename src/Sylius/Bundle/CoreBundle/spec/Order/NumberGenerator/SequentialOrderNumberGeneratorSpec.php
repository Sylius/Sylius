<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Order\NumberGenerator;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Order\NumberGenerator\SequentialOrderNumberGenerator;
use Sylius\Bundle\OrderBundle\NumberGenerator\OrderNumberGeneratorInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderSequenceInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class SequentialOrderNumberGeneratorSpec extends ObjectBehavior
{
    function let(
        EntityRepository $sequenceRepository,
        FactoryInterface $sequenceFactory,
        EntityManagerInterface $sequenceManager
    ) {
        $this->beConstructedWith($sequenceRepository, $sequenceFactory, $sequenceManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SequentialOrderNumberGenerator::class);
    }

    function it_implements_an_order_number_generator_interface()
    {
        $this->shouldImplement(OrderNumberGeneratorInterface::class);
    }

    function it_generates_an_order_number(
        EntityRepository $sequenceRepository,
        EntityManagerInterface $sequenceManager,
        OrderSequenceInterface $sequence,
        OrderInterface $order
    ) {
        $sequence->getIndex()->willReturn(6);
        $sequence->getVersion()->willReturn(7);

        $sequenceRepository->findOneBy([])->willReturn($sequence);

        $sequenceManager->lock($sequence, LockMode::OPTIMISTIC, 7)->shouldBeCalled();
        $sequence->incrementIndex()->shouldBeCalled();

        $this->generate($order)->shouldReturn('000000007');
    }

    function it_generates_an_order_number_when_sequence_is_null(
        EntityRepository $sequenceRepository,
        FactoryInterface $sequenceFactory,
        EntityManagerInterface $sequenceManager,
        OrderSequenceInterface $sequence,
        OrderInterface $order
    ) {
        $sequence->getIndex()->willReturn(0);
        $sequence->getVersion()->willReturn(1);

        $sequenceRepository->findOneBy([])->willReturn(null);

        $sequenceFactory->createNew()->willReturn($sequence);
        $sequenceManager->persist($sequence)->shouldBeCalled();

        $sequenceManager->lock($sequence, LockMode::OPTIMISTIC, 1)->shouldBeCalled();
        $sequence->incrementIndex()->shouldBeCalled();

        $this->generate($order)->shouldReturn('000000001');
    }
}
