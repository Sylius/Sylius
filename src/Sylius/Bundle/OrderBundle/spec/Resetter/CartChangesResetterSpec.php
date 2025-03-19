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

namespace spec\Sylius\Bundle\OrderBundle\Resetter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Resetter\CartChangesResetter;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderItemUnitInterface;

final class CartChangesResetterSpec extends ObjectBehavior
{
    function let(EntityManagerInterface $manager): void
    {
        $this->beConstructedWith($manager);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CartChangesResetter::class);
    }

    function it_does_nothing_if_cart_is_not_managed(
        EntityManagerInterface $manager,
        OrderInterface $cart,
    ): void {
        $manager->contains($cart)->willReturn(false);

        $manager->refresh($cart)->shouldNotBeCalled();

        $this->resetChanges($cart);
    }

    function it_resets_changes_for_cart_items_and_units(
        EntityManagerInterface $manager,
        UnitOfWork $unitOfWork,
        OrderInterface $cart,
        OrderItemInterface $item,
        OrderItemUnitInterface $unitNew,
        OrderItemUnitInterface $unitExisting,
        Collection $itemsCollection,
    ): void {
        $manager->contains($cart)->willReturn(true);
        $manager->getUnitOfWork()->willReturn($unitOfWork);

        $cart->getItems()->willReturn(new ArrayCollection([$item->getWrappedObject()]));

        $item->getUnits()->willReturn(new ArrayCollection([$unitNew->getWrappedObject(), $unitExisting->getWrappedObject()]));

        $unitOfWork->getEntityState($unitNew)->willReturn(UnitOfWork::STATE_NEW);
        $unitOfWork->getEntityState($unitExisting)->willReturn(UnitOfWork::STATE_MANAGED);

        $item->removeUnit($unitNew)->shouldBeCalled();
        $manager->refresh($item)->shouldBeCalled();
        $manager->refresh($cart)->shouldBeCalled();

        $this->resetChanges($cart);
    }
}
