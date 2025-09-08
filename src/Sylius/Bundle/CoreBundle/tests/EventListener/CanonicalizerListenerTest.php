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

use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\CanonicalizerListener;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;

final class CanonicalizerListenerTest extends TestCase
{
    private CanonicalizerInterface&MockObject $canonicalizer;

    private CanonicalizerListener $listener;

    protected function setUp(): void
    {
        $this->canonicalizer = $this->createMock(CanonicalizerInterface::class);
        $this->listener = new CanonicalizerListener($this->canonicalizer);
    }

    public function testItCanonicalizeUserUsernameOnPrePersistDoctrineEvent(): void
    {
        $event = $this->createMock(LifecycleEventArgs::class);
        $user = $this->createMock(ShopUserInterface::class);

        $event->method('getObject')->willReturn($user);

        $user->method('getUsername')->willReturn('testUser');
        $user->method('getEmail')->willReturn('test@email.com');

        $user->expects($this->once())->method('setUsernameCanonical')->with('testuser');
        $user->expects($this->once())->method('setEmailCanonical')->with('test@email.com');

        $this->canonicalizer->method('canonicalize')->willReturnMap([
            ['testUser', 'testuser'],
            ['test@email.com', 'test@email.com'],
        ]);

        $this->listener->prePersist($event);
    }

    public function testItCanonicalizeCustomerEmailOnPrePersistDoctrineEvent(): void
    {
        $event = $this->createMock(LifecycleEventArgs::class);
        $customer = $this->createMock(CustomerInterface::class);

        $event->method('getObject')->willReturn($customer);
        $customer->method('getEmail')->willReturn('testUser@Email.com');

        $customer->expects($this->once())->method('setEmailCanonical')->with('testuser@email.com');

        $this->canonicalizer
            ->method('canonicalize')
            ->with('testUser@Email.com')
            ->willReturn('testuser@email.com')
        ;

        $this->listener->prePersist($event);
    }

    public function testItCanonicalizeUserUsernameOnPreUpdateDoctrineEvent(): void
    {
        $event = $this->createMock(LifecycleEventArgs::class);
        $user = $this->createMock(ShopUserInterface::class);

        $event->method('getObject')->willReturn($user);
        $user->method('getUsername')->willReturn('testUser');
        $user->method('getEmail')->willReturn('test@email.com');

        $user->expects($this->once())->method('setUsernameCanonical')->with('testuser');
        $user->expects($this->once())->method('setEmailCanonical')->with('test@email.com');

        $this->canonicalizer->method('canonicalize')->willReturnMap([
            ['testUser', 'testuser'],
            ['test@email.com', 'test@email.com'],
        ]);

        $this->listener->preUpdate($event);
    }

    public function testItCanonicalizeCustomerEmailOnPreUpdateDoctrineEvent(): void
    {
        $event = $this->createMock(LifecycleEventArgs::class);
        $customer = $this->createMock(CustomerInterface::class);

        $event->method('getObject')->willReturn($customer);
        $customer->method('getEmail')->willReturn('testUser@Email.com');

        $customer
            ->expects($this->once())
            ->method('setEmailCanonical')
            ->with('testuser@email.com')
        ;

        $this->canonicalizer
            ->method('canonicalize')
            ->with('testUser@Email.com')
            ->willReturn('testuser@email.com')
        ;

        $this->listener->preUpdate($event);
    }

    public function testItCanonicalizeOnlyUserOrCustomerInterfaceImplementationOnPrePersist(): void
    {
        $event = $this->createMock(LifecycleEventArgs::class);
        $event->method('getObject')->willReturn(new \stdClass());

        $this->canonicalizer->expects($this->never())->method('canonicalize');

        $this->listener->prePersist($event);
    }

    public function testItCanonicalizeOnlyUserOrCustomerInterfaceImplementationOnPreUpdate(): void
    {
        $event = $this->createMock(LifecycleEventArgs::class);
        $event->method('getObject')->willReturn(new \stdClass());

        $this->canonicalizer->expects($this->never())->method('canonicalize');

        $this->listener->preUpdate($event);
    }
}
