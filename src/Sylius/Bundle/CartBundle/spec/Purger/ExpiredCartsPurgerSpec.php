<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CartBundle\Purger;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Repository\CartRepositoryInterface;

class ExpiredCartsPurgerSpec extends ObjectBehavior
{
    function let(ObjectManager $manager, CartRepositoryInterface $repository)
    {
        $this->beConstructedWith($manager, $repository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Purger\ExpiredCartsPurger');
    }

    function it_purge_cart($manager, $repository, CartInterface $cart)
    {
        $repository->findExpiredCarts()->shouldBeCalled()->willReturn([$cart]);
        $manager->remove($cart)->shouldBeCalled();
        $manager->flush()->shouldBeCalled();

        $this->purge();
    }
}
