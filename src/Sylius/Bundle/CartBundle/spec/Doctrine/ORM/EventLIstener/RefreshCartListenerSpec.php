<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CartBundle\EventListener;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Cart\Model\CartInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class RefreshCartListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Doctrine\ORM\EventListener\RefreshCartListener');
    }

    function it_refresh_total_only_on_cart(
        CartInterface $cart,
        OnFlushEventArgs $args,
        EntityManager $entityManager,
        UnitOfWork $unitOfWork,
        Collection $entities,
        \Iterator $iterator
    ) {
        $args->getEntityManager()->willReturn($entityManager);
        $entityManager->getUnitOfWork()->willReturn($unitOfWork);
        $unitOfWork->getScheduledEntityUpdates()->willReturn($entities);
        $entities->getIterator()->willReturn($iterator);

        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, false);
        $iterator->current()->willReturn($cart);
        $iterator->next()->shouldBeCalled();

        $cart->calculateTotal()->shouldBeCalled();
        $cart->isEmpty()->willReturn(false);

        $this->onFlush($args);
    }

    function it_clears_adjustments_on_empty_cart(
        CartInterface $cart,
        OnFlushEventArgs $args,
        EntityManager $entityManager,
        UnitOfWork $unitOfWork,
        Collection $entities,
        \Iterator $iterator
    ) {
        $args->getEntityManager()->willReturn($entityManager);
        $entityManager->getUnitOfWork()->willReturn($unitOfWork);
        $unitOfWork->getScheduledEntityUpdates()->willReturn($entities);
        $entities->getIterator()->willReturn($iterator);

        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, false);
        $iterator->current()->willReturn($cart);
        $iterator->next()->shouldBeCalled();

        $cart->calculateTotal()->shouldBeCalled();
        $cart->isEmpty()->willReturn(true);
        $cart->clearAdjustments()->shouldBeCalled();

        $this->onFlush($args);
    }

    function it_does_not_clear_adjustments_on_cart_with_items(
        CartInterface $cart,
        OnFlushEventArgs $args,
        EntityManager $entityManager,
        UnitOfWork $unitOfWork,
        Collection $entities,
        \Iterator $iterator
    ) {
        $args->getEntityManager()->willReturn($entityManager);
        $entityManager->getUnitOfWork()->willReturn($unitOfWork);
        $unitOfWork->getScheduledEntityUpdates()->willReturn($entities);
        $entities->getIterator()->willReturn($iterator);

        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, false);
        $iterator->current()->willReturn($cart);
        $iterator->next()->shouldBeCalled();

        $cart->calculateTotal()->shouldBeCalled();
        $cart->isEmpty()->willReturn(false);
        $cart->clearAdjustments()->shouldNotBeCalled();

        $this->onFlush($args);
    }

    function it_does_not_perform_any_action_on_different_entity(
        AddressInterface $address,
        CartInterface $cart,
        OnFlushEventArgs $args,
        EntityManager $entityManager,
        UnitOfWork $unitOfWork,
        Collection $entities,
        \Iterator $iterator
    ) {
        $args->getEntityManager()->willReturn($entityManager);
        $entityManager->getUnitOfWork()->willReturn($unitOfWork);
        $unitOfWork->getScheduledEntityUpdates()->willReturn($entities);
        $entities->getIterator()->willReturn($iterator);

        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, true, false);
        $iterator->current()->willReturn($address);
        $iterator->next()->shouldBeCalled();

        $cart->calculateTotal()->shouldNotBeCalled();
        $cart->clearAdjustments()->shouldNotBeCalled();

        $iterator->rewind()->shouldBeCalled();
        $iterator->current()->willReturn($cart);
        $iterator->next()->shouldBeCalled();

        $cart->calculateTotal()->shouldBeCalled();
        $cart->isEmpty()->willReturn(false);

        $this->onFlush($args);
    }
}
