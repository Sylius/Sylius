<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\User\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;

class DefaultUsernameORMListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\EventListener\DefaultUsernameORMListener');
    }

    function it_update_username_on_pre_persit(
        LifecycleEventArgs $event,
        UserInterface $user,
        CustomerInterface $customer
    ) {
        $event->getEntity()->shouldBeCalled()->willReturn($user);
        $user->getCustomer()->shouldBeCalled()->willReturn($customer);

        $customer->getEmail()->shouldBeCalled()->willReturn('email@email.com');
        $user->getUsername()->shouldBeCalled()->willReturn('user');

        $user->setUsername('email@email.com')->shouldBeCalled();

        $this->prePersist($event);
    }

    function it_update_username_on_pre_update(
        LifecycleEventArgs $event,
        UserInterface $user,
        CustomerInterface $customer,
        EntityManager $entityManager
    ) {
        $event->getEntityManager()->shouldBeCalled()->willReturn($entityManager);

        $event->getEntity()->shouldBeCalled()->willReturn($customer);
        $customer->getUser()->shouldBeCalled()->willReturn($user);

        $customer->getEmail()->shouldBeCalled()->willReturn('email@email.com');
        $user->getUsername()->shouldBeCalled()->willReturn('user');

        $user->setUsername('email@email.com')->shouldBeCalled();

        $entityManager->persist($user);
        $entityManager->flush($user);

        $this->preUpdate($event);
    }
}
