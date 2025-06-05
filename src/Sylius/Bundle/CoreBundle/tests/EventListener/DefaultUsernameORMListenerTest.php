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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\DefaultUsernameORMListener;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class DefaultUsernameORMListenerTest extends TestCase
{
    private DefaultUsernameORMListener $listener;

    private MockObject&OnFlushEventArgs $onFlushEventArgs;

    private EntityManagerInterface&MockObject $entityManager;

    private MockObject&UnitOfWork $unitOfWork;

    private CustomerInterface&MockObject $customer;

    private MockObject&ShopUserInterface $user;

    private ClassMetadata&MockObject $userMetadata;

    protected function setUp(): void
    {
        $this->onFlushEventArgs = $this->createMock(OnFlushEventArgs::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->unitOfWork = $this->createMock(UnitOfWork::class);
        $this->customer = $this->createMock(CustomerInterface::class);
        $this->user = $this->createMock(ShopUserInterface::class);
        $this->userMetadata = $this->createMock(ClassMetadata::class);
        $this->listener = new DefaultUsernameORMListener();
    }

    public function testSetsUsernamesOnCustomerCreate(): void
    {
        $this->onFlushEventArgs->method('getObjectManager')->willReturn($this->entityManager);
        $this->entityManager->method('getUnitOfWork')->willReturn($this->unitOfWork);

        $this->unitOfWork->method('getScheduledEntityInsertions')->willReturn([$this->customer]);
        $this->unitOfWork->method('getScheduledEntityUpdates')->willReturn([]);

        $this->user->method('getUsername')->willReturn(null);
        $this->user->method('getUsernameCanonical')->willReturn(null);
        $this->customer->method('getUser')->willReturn($this->user);
        $this->customer->method('getEmail')->willReturn('customer+extra@email.com');
        $this->customer->method('getEmailCanonical')->willReturn('customer@email.com');

        $this->user->expects($this->once())->method('setUsername')->with('customer+extra@email.com');
        $this->user->expects($this->once())->method('setUsernameCanonical')->with('customer@email.com');

        $this->entityManager->method('getClassMetadata')->willReturn($this->userMetadata);
        $this->unitOfWork->expects($this->once())->method('recomputeSingleEntityChangeSet')->with($this->userMetadata, $this->user);

        $this->listener->onFlush($this->onFlushEventArgs);
    }

    public function testSetsUsernamesOnCustomerUpdateWhenUserIsAssociated(): void
    {
        $this->onFlushEventArgs->method('getObjectManager')->willReturn($this->entityManager);
        $this->entityManager->method('getUnitOfWork')->willReturn($this->unitOfWork);

        $this->unitOfWork->method('getScheduledEntityInsertions')->willReturn([$this->user]);
        $this->unitOfWork->method('getScheduledEntityUpdates')->willReturn([]);

        $this->user->method('getUsername')->willReturn(null);
        $this->user->method('getUsernameCanonical')->willReturn(null);
        $this->user->method('getCustomer')->willReturn($this->customer);
        $this->customer->method('getEmail')->willReturn('customer+extra@email.com');
        $this->customer->method('getEmailCanonical')->willReturn('customer@email.com');

        $this->user->expects($this->once())->method('setUsername')->with('customer+extra@email.com');
        $this->user->expects($this->once())->method('setUsernameCanonical')->with('customer@email.com');

        $this->entityManager->method('getClassMetadata')->willReturn($this->userMetadata);
        $this->unitOfWork->expects($this->once())->method('recomputeSingleEntityChangeSet')->with($this->userMetadata, $this->user);

        $this->listener->onFlush($this->onFlushEventArgs);
    }

    public function testItUpdatesUsernamesOnCustomerEmailChange(): void
    {
        $this->onFlushEventArgs->method('getObjectManager')->willReturn($this->entityManager);
        $this->entityManager->method('getUnitOfWork')->willReturn($this->unitOfWork);
        $this->unitOfWork->method('getScheduledEntityInsertions')->willReturn([]);
        $this->unitOfWork->method('getScheduledEntityUpdates')->willReturn([$this->customer]);
        $this->customer->method('getUser')->willReturn($this->user);
        $this->customer->method('getEmail')->willReturn('customer+extra@email.com');
        $this->customer->method('getEmailCanonical')->willReturn('customer@email.com');
        $this->user->method('getUsername')->willReturn('user+extra@email.com');
        $this->user->method('getUsernameCanonical')->willReturn('customer@email.com');
        $this->user->expects($this->once())->method('setUsername')->with('customer+extra@email.com');
        $this->user->expects($this->once())->method('setUsernameCanonical')->with('customer@email.com');
        $this->entityManager->method('getClassMetadata')->willReturn($this->userMetadata);

        $this->unitOfWork
            ->expects($this->once())
            ->method('recomputeSingleEntityChangeSet')
            ->with($this->userMetadata, $this->user)
        ;

        $this->listener->onFlush($this->onFlushEventArgs);
    }

    public function testItUpdatesUsernamesOnCustomerEmailCanonicalChange(): void
    {
        $this->onFlushEventArgs->method('getObjectManager')->willReturn($this->entityManager);
        $this->entityManager->method('getUnitOfWork')->willReturn($this->unitOfWork);
        $this->unitOfWork->method('getScheduledEntityInsertions')->willReturn([]);
        $this->unitOfWork->method('getScheduledEntityUpdates')->willReturn([$this->customer]);
        $this->customer->method('getUser')->willReturn($this->user);
        $this->customer->method('getEmail')->willReturn('customer+extra@email.com');
        $this->customer->method('getEmailCanonical')->willReturn('customer@email.com');
        $this->user->method('getUsername')->willReturn('customer+extra@email.com');
        $this->user->method('getUsernameCanonical')->willReturn('user@email.com');
        $this->user->expects($this->once())->method('setUsername')->with('customer+extra@email.com');
        $this->user->expects($this->once())->method('setUsernameCanonical')->with('customer@email.com');
        $this->entityManager->method('getClassMetadata')->willReturn($this->userMetadata);

        $this->unitOfWork
            ->expects($this->once())
            ->method('recomputeSingleEntityChangeSet')
            ->with($this->userMetadata, $this->user)
        ;

        $this->listener->onFlush($this->onFlushEventArgs);
    }

    public function testItDoesNotUpdateUsernamesWhenCustomerEmailsAreTheSame(): void
    {
        $customer = $this->createMock(CustomerInterface::class);
        $user = $this->createMock(ShopUserInterface::class);

        $this->onFlushEventArgs->method('getObjectManager')->willReturn($this->entityManager);
        $this->entityManager->method('getUnitOfWork')->willReturn($this->unitOfWork);
        $this->unitOfWork->method('getScheduledEntityInsertions')->willReturn([]);
        $this->unitOfWork->method('getScheduledEntityUpdates')->willReturn([$customer]);

        $customer->method('getUser')->willReturn($user);
        $customer->method('getEmail')->willReturn('customer+extra@email.com');
        $customer->method('getEmailCanonical')->willReturn('customer@email.com');

        $user->method('getUsername')->willReturn('customer+extra@email.com');
        $user->method('getUsernameCanonical')->willReturn('customer@email.com');

        $user->expects($this->never())->method('setUsername');
        $user->expects($this->never())->method('setUsernameCanonical');
        $this->unitOfWork->expects($this->never())->method('recomputeSingleEntityChangeSet');

        $this->listener->onFlush($this->onFlushEventArgs);
    }

    public function testItDoesNothingOnCustomerCreateWhenNoUserAssociated(): void
    {
        $customer = $this->createMock(CustomerInterface::class);

        $this->onFlushEventArgs->method('getObjectManager')->willReturn($this->entityManager);
        $this->entityManager->method('getUnitOfWork')->willReturn($this->unitOfWork);
        $this->unitOfWork->method('getScheduledEntityInsertions')->willReturn([$customer]);
        $this->unitOfWork->method('getScheduledEntityUpdates')->willReturn([]);

        $customer->method('getUser')->willReturn(null);

        $this->unitOfWork->expects($this->never())->method('recomputeSingleEntityChangeSet');

        $this->listener->onFlush($this->onFlushEventArgs);
    }

    public function testItDoesNothingOnCustomerUpdateWhenNoUserAssociated(): void
    {
        $customer = $this->createMock(CustomerInterface::class);

        $this->onFlushEventArgs->method('getObjectManager')->willReturn($this->entityManager);
        $this->entityManager->method('getUnitOfWork')->willReturn($this->unitOfWork);
        $this->unitOfWork->method('getScheduledEntityInsertions')->willReturn([]);
        $this->unitOfWork->method('getScheduledEntityUpdates')->willReturn([$customer]);

        $customer->method('getUser')->willReturn(null);
        $customer->method('getEmail')->willReturn('customer@email.com');

        $this->unitOfWork->expects($this->never())->method('recomputeSingleEntityChangeSet');

        $this->listener->onFlush($this->onFlushEventArgs);
    }

    public function testItDoesNothingWhenThereAreNoObjectsScheduledInTheUnitOfWork(): void
    {
        $this->onFlushEventArgs->method('getObjectManager')->willReturn($this->entityManager);
        $this->entityManager->method('getUnitOfWork')->willReturn($this->unitOfWork);

        $this->unitOfWork->method('getScheduledEntityInsertions')->willReturn([]);
        $this->unitOfWork->method('getScheduledEntityUpdates')->willReturn([]);

        $this->unitOfWork->expects($this->never())->method('recomputeSingleEntityChangeSet');

        $this->listener->onFlush($this->onFlushEventArgs);
    }

    public function testItDoesNothingWhenThereAreOtherObjectsThanCustomer(): void
    {
        $stdObject1 = new \stdClass();
        $stdObject2 = new \stdClass();

        $this->onFlushEventArgs->method('getObjectManager')->willReturn($this->entityManager);
        $this->entityManager->method('getUnitOfWork')->willReturn($this->unitOfWork);

        $this->unitOfWork->method('getScheduledEntityInsertions')->willReturn([$stdObject1]);
        $this->unitOfWork->method('getScheduledEntityUpdates')->willReturn([$stdObject2]);

        $this->unitOfWork->expects($this->never())->method('recomputeSingleEntityChangeSet');

        $this->listener->onFlush($this->onFlushEventArgs);
    }
}
