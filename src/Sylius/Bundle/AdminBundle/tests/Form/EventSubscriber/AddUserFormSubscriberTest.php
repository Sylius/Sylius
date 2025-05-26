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

namespace Tests\Sylius\Bundle\AdminBundle\Form\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Form\EventSubscriber\AddUserFormSubscriber;
use Sylius\Component\User\Model\UserAwareInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;

final class AddUserFormSubscriberTest extends TestCase
{
    private AddUserFormSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->subscriber = new AddUserFormSubscriber();
    }

    public function testItImplementsEventSubscriberInterface(): void
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->subscriber);
    }

    public function testItDoesNotReplaceUserFormWhenUserHasAnId(): void
    {
        $event = $this->createMock(FormEvent::class);
        $form = $this->createMock(Form::class);
        $customer = $this->createMock(UserAwareInterface::class);
        $user = $this->createMock(UserInterface::class);

        $event->method('getData')->willReturn($customer);
        $event->method('getForm')->willReturn($form);

        $customer->method('getUser')->willReturn($user);
        $user->method('getId')->willReturn(1);
        $user->method('getPlainPassword')->willReturn(null);

        $customer->expects($this->never())->method('setUser');
        $event->expects($this->never())->method('setData');
        $form->expects($this->never())->method('remove');
        $form->expects($this->never())->method('add');

        $this->subscriber->submit($event);
    }

    public function testItDoesNotReplaceUserFormWhenPasswordIsSet(): void
    {
        $event = $this->createMock(FormEvent::class);
        $form = $this->createMock(Form::class);
        $customer = $this->createMock(UserAwareInterface::class);
        $user = $this->createMock(UserInterface::class);

        $event->method('getData')->willReturn($customer);
        $event->method('getForm')->willReturn($form);

        $customer->method('getUser')->willReturn($user);
        $user->method('getId')->willReturn(null);
        $user->method('getPlainPassword')->willReturn('password');

        $customer->expects($this->never())->method('setUser');
        $event->expects($this->never())->method('setData');
        $form->expects($this->never())->method('remove');
        $form->expects($this->never())->method('add');

        $this->subscriber->submit($event);
    }

    public function testItThrowsInvalidArgumentExceptionWhenDataIsNotUserAware(): void
    {
        $event = $this->createMock(FormEvent::class);
        $form = $this->createMock(Form::class);
        $user = $this->createMock(UserInterface::class);

        $event->method('getData')->willReturn($user);
        $event->method('getForm')->willReturn($form);

        $this->expectException(\InvalidArgumentException::class);

        $this->subscriber->submit($event);
    }
}
