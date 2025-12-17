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

namespace Tests\Sylius\Bundle\CoreBundle\Form\EventSubscriber;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\CustomerRegistrationFormSubscriber;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

final class CustomerRegistrationFormSubscriberTest extends TestCase
{
    private MockObject&RepositoryInterface $customerRepository;

    private CustomerRegistrationFormSubscriber $customerRegistrationFormSubscriber;

    protected function setUp(): void
    {
        $this->customerRepository = $this->createMock(RepositoryInterface::class);
        $this->customerRegistrationFormSubscriber = new CustomerRegistrationFormSubscriber($this->customerRepository);
    }

    public function testEventSubscriberInstance(): void
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->customerRegistrationFormSubscriber);
    }

    public function testListensOnPreSubmitDataEvent(): void
    {
        $this->assertSame(
            [FormEvents::PRE_SUBMIT => 'preSubmit'],
            $this->customerRegistrationFormSubscriber->getSubscribedEvents(),
        );
    }

    public function testSetsUserForexistingCustomer(): void
    {
        $event = $this->createMock(FormEvent::class);
        $form = $this->createMock(FormInterface::class);
        $customer = $this->createMock(CustomerInterface::class);
        $existingCustomer = $this->createMock(CustomerInterface::class);
        $user = $this->createMock(ShopUserInterface::class);

        $event->expects($this->once())->method('getForm')->willReturn($form);
        $form->expects($this->once())->method('getData')->willReturn($customer);
        $event->expects($this->once())->method('getData')->willReturn(['email' => 'sylius@example.com']);

        $this->customerRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'sylius@example.com'])
            ->willReturn($existingCustomer)
        ;

        $existingCustomer->expects($this->once())->method('getUser')->willReturn(null);
        $customer->expects($this->once())->method('getUser')->willReturn($user);
        $existingCustomer->expects($this->once())->method('setUser')->with($user);
        $form->expects($this->once())->method('setData')->with($existingCustomer)->willReturn($form);

        $this->customerRegistrationFormSubscriber->preSubmit($event);
    }

    public function testDoesNothingIfDataIsNotCustomerType(): void
    {
        $event = $this->createMock(FormEvent::class);
        $form = $this->createMock(FormInterface::class);
        $user = $this->createMock(ShopUserInterface::class);

        $event->expects($this->once())->method('getForm')->willReturn($form);
        $form->expects($this->once())->method('getData')->willReturn($user);
        $event->expects($this->once())->method('getData')->willReturn(['email' => 'sylius@example.com']);

        $this->customerRepository
            ->expects($this->never())
            ->method('findOneBy')
            ->with(['email' => 'sylius@example.com'])
        ;

        $this->customerRegistrationFormSubscriber->preSubmit($event);
    }

    public function testDoesNotSetUserIfCustomerWithGivenEmailHasSetUser(): void
    {
        $event = $this->createMock(FormEvent::class);
        $form = $this->createMock(FormInterface::class);
        $customer = $this->createMock(CustomerInterface::class);
        $existingCustomer = $this->createMock(CustomerInterface::class);
        $user = $this->createMock(ShopUserInterface::class);

        $event->expects($this->once())->method('getForm')->willReturn($form);
        $form->expects($this->once())->method('getData')->willReturn($customer);
        $event->expects($this->once())->method('getData')->willReturn(['email' => 'sylius@example.com']);

        $this->customerRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'sylius@example.com'])
            ->willReturn($existingCustomer)
        ;

        $existingCustomer->expects($this->once())->method('getUser')->willReturn($user);
        $existingCustomer->expects($this->never())->method('setUser')->with($user);
        $form->expects($this->never())->method('setData')->with($existingCustomer);

        $this->customerRegistrationFormSubscriber->preSubmit($event);
    }
}
