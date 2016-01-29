<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Originator\Originator;

use Doctrine\Common\Persistence\ManagerRegistry;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Originator\Model\OriginAwareInterface;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class OriginatorSpec extends ObjectBehavior
{
    public function let(ManagerRegistry $manager)
    {
        $this->beConstructedWith($manager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Originator\Originator\Originator');
    }

    public function it_should_be_Sylius_originator()
    {
        $this->shouldImplement(OriginatorInterface::class);
    }

    public function it_throws_exception_if_origin_is_not_an_object(OriginAwareInterface $originAware)
    {
        $this->shouldThrow(\InvalidArgumentException::class)->duringSetOrigin($originAware, 'umpirsky');
    }

    public function it_throws_exception_if_origin_has_no_id(OriginAwareInterface $originAware, FakeEntity $entity)
    {
        $entity->getId(null)->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->duringSetOrigin($originAware, $entity);
    }

    public function it_sets_origin(OriginAwareInterface $originAware, FakeEntity $entity)
    {
        $entity->getId()->willReturn(5);

        $originAware->setOriginId(5)->willReturn($originAware);
        $originAware->setOriginType(Argument::any())->shouldBeCalled();

        $this->setOrigin($originAware, $entity);
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

    public function it_gets_origin($manager, RepositoryInterface $repository, OriginAwareInterface $originAware, FakeEntity $entity)
    {
        $originAware->getOriginId()->willReturn(5);
        $originAware->getOriginType()->willReturn('Sylius\Component\Originator\Model\FakeEntity');

        $manager->getRepository('Sylius\Component\Originator\Model\FakeEntity')->willReturn($repository);

        $repository->findOneBy(['id' => 5])->willReturn($entity);

        $this->getOrigin($originAware)->shouldReturn($entity);
    }
}

class FakeEntity
{
    public function getId($value = 5)
    {
        return $value;
    }
}
