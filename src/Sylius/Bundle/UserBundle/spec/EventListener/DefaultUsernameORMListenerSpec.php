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
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;
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

    function it_sets_username_on_customer_create(
        OnFlushEventArgs $onFlushEventArgs,
        EntityManager $entityManager,
        UnitOfWork $unitOfWork,
        CustomerInterface $customer,
        UserInterface $user,
        ClassMetadata $userMetadata
    ) {
        $onFlushEventArgs->getEntityManager()->willReturn($entityManager);
        $entityManager->getUnitOfWork()->willReturn($unitOfWork);

        $unitOfWork->getScheduledEntityInsertions()->willReturn([$customer]);
        $unitOfWork->getScheduledEntityUpdates()->willReturn([]);

        $user->getUsername()->willReturn(null);
        $customer->getUser()->willReturn($user);
        $customer->getEmail()->willReturn('customer@email.com');

        $user->setUsername('customer@email.com')->shouldBeCalled();
        $entityManager->persist($user)->shouldBeCalled();
        $entityManager->getClassMetadata(get_class($user->getWrappedObject()))->willReturn($userMetadata);
        $unitOfWork->recomputeSingleEntityChangeSet($userMetadata, $user)->shouldBeCalled();
        $this->onFlush($onFlushEventArgs);
    }

    function it_does_nothing_on_customer_create_when_no_user_associated(
        OnFlushEventArgs $onFlushEventArgs,
        EntityManager $entityManager,
        UnitOfWork $unitOfWork,
        CustomerInterface $customer
    ) {
        $onFlushEventArgs->getEntityManager()->willReturn($entityManager);
        $entityManager->getUnitOfWork()->willReturn($unitOfWork);

        $unitOfWork->getScheduledEntityInsertions()->willReturn([$customer]);
        $unitOfWork->getScheduledEntityUpdates()->willReturn([]);

        $customer->getUser()->willReturn(null);
        $customer->getEmail()->willReturn('customer@email.com');

        $entityManager->persist(Argument::any())->shouldNotBeCalled();
        $unitOfWork->recomputeSingleEntityChangeSet(Argument::any())->shouldNotBeCalled();
        $this->onFlush($onFlushEventArgs);
    }

    function it_updates_username_on_customer_email_change(
        OnFlushEventArgs $onFlushEventArgs,
        EntityManager $entityManager,
        UnitOfWork $unitOfWork,
        CustomerInterface $customer,
        UserInterface $user,
        ClassMetadata $userMetadata
    ) {
        $onFlushEventArgs->getEntityManager()->willReturn($entityManager);
        $entityManager->getUnitOfWork()->willReturn($unitOfWork);

        $unitOfWork->getScheduledEntityInsertions()->willReturn([]);
        $unitOfWork->getScheduledEntityUpdates()->willReturn([$customer]);

        $user->getUsername()->willReturn('user@email.com');
        $customer->getUser()->willReturn($user);
        $customer->getEmail()->willReturn('customer@email.com');

        $user->setUsername('customer@email.com')->shouldBeCalled();
        $entityManager->persist($user)->shouldBeCalled();
        $entityManager->getClassMetadata(get_class($user->getWrappedObject()))->willReturn($userMetadata);
        $unitOfWork->recomputeSingleEntityChangeSet($userMetadata, $user)->shouldBeCalled();
        $this->onFlush($onFlushEventArgs);
    }

    function it_does_not_update_username_when_customer_email_is_the_same(
        OnFlushEventArgs $onFlushEventArgs,
        EntityManager $entityManager,
        UnitOfWork $unitOfWork,
        CustomerInterface $customer,
        UserInterface $user,
        ClassMetadata $userMetadata
    ) {
        $onFlushEventArgs->getEntityManager()->willReturn($entityManager);
        $entityManager->getUnitOfWork()->willReturn($unitOfWork);

        $unitOfWork->getScheduledEntityInsertions()->willReturn([]);
        $unitOfWork->getScheduledEntityUpdates()->willReturn([$customer]);

        $user->getUsername()->willReturn('customer@email.com');
        $customer->getUser()->willReturn($user);
        $customer->getEmail()->willReturn('customer@email.com');

        $user->setUsername('customer@email.com')->shouldNotBeCalled();
        $entityManager->persist($user)->shouldNotBeCalled();
        $unitOfWork->recomputeSingleEntityChangeSet($userMetadata, $user)->shouldNotBeCalled();
        $this->onFlush($onFlushEventArgs);
    }

    function it_does_nothing_on_customer_update_when_no_user_associated(
        OnFlushEventArgs $onFlushEventArgs,
        EntityManager $entityManager,
        UnitOfWork $unitOfWork,
        CustomerInterface $customer
    ) {
        $onFlushEventArgs->getEntityManager()->willReturn($entityManager);
        $entityManager->getUnitOfWork()->willReturn($unitOfWork);

        $unitOfWork->getScheduledEntityInsertions()->willReturn([]);
        $unitOfWork->getScheduledEntityUpdates()->willReturn([$customer]);

        $customer->getUser()->willReturn(null);
        $customer->getEmail()->willReturn('customer@email.com');

        $entityManager->persist(Argument::any())->shouldNotBeCalled();
        $unitOfWork->recomputeSingleEntityChangeSet(Argument::any())->shouldNotBeCalled();
        $this->onFlush($onFlushEventArgs);
    }

    function it_does_nothing_when_there_are_no_customers_created(
        OnFlushEventArgs $onFlushEventArgs,
        EntityManager $entityManager,
        UnitOfWork $unitOfWork
    ) {
        $onFlushEventArgs->getEntityManager()->willReturn($entityManager);
        $entityManager->getUnitOfWork()->willReturn($unitOfWork);

        $unitOfWork->getScheduledEntityInsertions()->willReturn([]);
        $unitOfWork->getScheduledEntityUpdates()->willReturn([]);

        $entityManager->persist(Argument::any())->shouldNotBeCalled();
        $unitOfWork->recomputeSingleEntityChangeSet(Argument::any())->shouldNotBeCalled();
        $this->onFlush($onFlushEventArgs);
    }

    function it_does_nothing_when_there_are_other_objects_than_customer(
        OnFlushEventArgs $onFlushEventArgs,
        EntityManager $entityManager,
        UnitOfWork $unitOfWork,
        \stdClass $stdObject,
        \stdClass $stdObject2
    ) {
        $onFlushEventArgs->getEntityManager()->willReturn($entityManager);
        $entityManager->getUnitOfWork()->willReturn($unitOfWork);

        $unitOfWork->getScheduledEntityInsertions()->willReturn([$stdObject]);
        $unitOfWork->getScheduledEntityUpdates()->willReturn([$stdObject2]);

        $entityManager->persist(Argument::any())->shouldNotBeCalled();
        $unitOfWork->recomputeSingleEntityChangeSet(Argument::any())->shouldNotBeCalled();
        $this->onFlush($onFlushEventArgs);
    }
}
