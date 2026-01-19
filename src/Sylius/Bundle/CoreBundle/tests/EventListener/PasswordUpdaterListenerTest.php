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

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\PasswordUpdaterListener;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class PasswordUpdaterListenerTest extends TestCase
{
    private MockObject&PasswordUpdaterInterface $passwordUpdater;

    private PasswordUpdaterListener $passwordUpdaterListener;

    protected function setUp(): void
    {
        $this->passwordUpdater = $this->createMock(PasswordUpdaterInterface::class);
        $this->passwordUpdaterListener = new PasswordUpdaterListener($this->passwordUpdater);
    }

    public function testUpdatesPasswordForCustomer(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $user = $this->createMock(UserInterface::class);
        $customer = $this->createMock(CustomerInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($customer);
        $customer->expects($this->once())->method('getUser')->willReturn($user);
        $user->expects($this->once())->method('getPlainPassword')->willReturn('password123');
        $this->passwordUpdater->expects($this->once())->method('updatePassword')->with($user);

        $this->passwordUpdaterListener->customerUpdateEvent($event);
    }

    public function testDoesNotUpdatePasswordIfSubjectIsNotInstanceOfCustomerInterface(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $user = $this->createMock(UserInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($user);

        $this->expectException(InvalidArgumentException::class);

        $this->passwordUpdaterListener->customerUpdateEvent($event);
    }

    public function testDoesNotUpdatePasswordIfCustomerDoesNotHaveUser(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $customer = $this->createMock(CustomerInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($customer);
        $customer->expects($this->once())->method('getUser')->willReturn(null);
        $this->passwordUpdater->expects($this->never())->method('updatePassword')->with(null);

        $this->passwordUpdaterListener->customerUpdateEvent($event);
    }
}
