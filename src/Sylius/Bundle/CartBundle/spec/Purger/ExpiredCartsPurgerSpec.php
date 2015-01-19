<?php

namespace spec\Sylius\Bundle\CartBundle\Purger;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Repository\CartRepositoryInterface;
use Sylius\Component\Resource\Manager\ResourceManagerInterface;

class ExpiredCartsPurgerSpec extends ObjectBehavior
{
    function let(ResourceManagerInterface $manager, CartRepositoryInterface $repository)
    {
        $this->beConstructedWith($manager, $repository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Purger\ExpiredCartsPurger');
    }

    function it_purge_cart($manager, $repository, CartInterface $cart)
    {
        $repository->findExpiredCarts()->shouldBeCalled()->willReturn(array($cart));
        $manager->remove($cart)->shouldBeCalled();
        $manager->flush()->shouldBeCalled();

        $this->purge();
    }
}
