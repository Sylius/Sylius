<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OrderBundle\Generator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\OrderBundle\Model\OrderInterface;
use Sylius\Bundle\OrderBundle\Repository\NumberRepositoryInterface;

/**
 * @author Myke Hines <myke@webhines.com>
 */
class HashOrderNumberGeneratorSpec extends ObjectBehavior
{
    public function let(NumberRepositoryInterface $numberRepository)
    {
        $this->beConstructedWith($numberRepository, 9, 100);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\Generator\HashOrderNumberGenerator');
    }

    public function it_implements_Sylius_order_number_generator_interface()
    {
        $this->shouldImplement('Sylius\Bundle\OrderBundle\Generator\OrderNumberGenerator');
    }

    public function it_finds_order_number(NumberRepositoryInterface $numberRepository, OrderInterface $order)
    {
        $numberRepository->isUsed(Argument::any())->willReturn(false);
        $order->setNumber(Argument::any())->shouldBeCalled();
        $this->generate($order);
    }

    public function getMatchers()
    {
        return array(
            'haveLength' => function ($subject, $key) {
                return strlen($subject) === $key;
            }
        );
    }
}
