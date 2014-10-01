<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Originator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\OriginAwareInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\Promotion;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class OriginatorSpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $em)
    {
        $this->beConstructedWith($em);
    }

    public function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Originator\Originator');
    }

    public function it_should_be_Sylius_originator()
    {
        $this->shouldImplement('Sylius\Component\Core\Originator\OriginatorInterface');
    }

    public function it_throw_exception_if_origin_is_not_an_object(OriginAwareInterface $originAware)
    {
        $this->shouldThrow('InvalidArgumentException')->duringSetOrigin($originAware, 'umpirsky');
    }

    public function it_throw_exception_if_origin_have_no_id(OriginAwareInterface $originAware, PromotionInterface $promotion)
    {
        $this->shouldThrow('InvalidArgumentException')->duringSetOrigin($originAware, $promotion);
    }

    public function it_sets_origin(OriginAwareInterface $originAware, Promotion $promotion)
    {
        $promotion->getId()->shouldBeCalled()->willReturn(5);

        $originAware->setOriginId(5)->shouldBeCalled()->willReturn($originAware);
        $originAware->setOriginType(Argument::any())->shouldBeCalled();

        $this->setOrigin($originAware, $promotion);
    }

    public function it_returns_null_if_there_is_no_origin_id(OriginAwareInterface $originAware)
    {
        $originAware->getOriginId()->willReturn(null);

        $this->getOrigin($originAware)->shouldReturn(null);
    }

    public function it_returns_null_if_there_is_no_origin_type(OriginAwareInterface $originAware)
    {
        $originAware->getOriginId()->willReturn(5);
        $originAware->getOriginType()->willReturn(null);

        $this->getOrigin($originAware)->shouldReturn(null);
    }

    public function it_gets_origin($em, RepositoryInterface $repository, OriginAwareInterface $originAware, PromotionInterface $promotion)
    {
        $originAware->getOriginId()->willReturn(5);
        $originAware->getOriginType()->willReturn('Sylius\Component\Promotion\Model\Promotion');

        $em->getRepository('Sylius\Component\Promotion\Model\Promotion')->shouldBeCalled()->willReturn($repository);
        $repository->find(5)->shouldBeCalled()->willReturn($promotion);

        $this->getOrigin($originAware)->shouldReturn($promotion);
    }
}
