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
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\CustomerDefaultAddressListener;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;

final class CustomerDefaultAddressListenerTest extends TestCase
{
    private CustomerDefaultAddressListener $listener;

    protected function setUp(): void
    {
        $this->listener = new CustomerDefaultAddressListener();
    }

    public function testAddsTheAddressAsDefaultToTheCustomerOnPreCreateResourceControllerEvent(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $address = $this->createMock(AddressInterface::class);
        $customer = $this->createMock(CustomerInterface::class);

        $event->method('getSubject')->willReturn($address);
        $address->expects($this->once())->method('getId')->willReturn(null);
        $address->expects($this->once())->method('getCustomer')->willReturn($customer);

        $customer->expects($this->once())->method('getDefaultAddress')->willReturn(null);
        $customer->expects($this->once())->method('setDefaultAddress')->with($address);

        $this->listener->preCreate($event);
    }

    public function testDoesNotSetAddressAsDefaultIfCustomerAlreadyHaveADefaultAddress(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $address = $this->createMock(AddressInterface::class);
        $customer = $this->createMock(CustomerInterface::class);
        $anotherAddress = $this->createMock(AddressInterface::class);

        $event->method('getSubject')->willReturn($address);
        $address->expects($this->once())->method('getId')->willReturn(null);
        $address->expects($this->once())->method('getCustomer')->willReturn($customer);

        $customer->expects($this->once())->method('getDefaultAddress')->willReturn($anotherAddress);
        $customer->expects($this->never())->method('setDefaultAddress');

        $this->listener->preCreate($event);
    }

    public function testThrowsAnExceptionIfEventSubjectIsNotAnAddress(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $event->method('getSubject')->willReturn(new \stdClass());

        $this->expectException(InvalidArgumentException::class);

        $this->listener->preCreate($event);
    }

    public function testDoesNothingIfAddressDoesHaveAnId(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $address = $this->createMock(AddressInterface::class);

        $event->method('getSubject')->willReturn($address);
        $address->expects($this->once())->method('getId')->willReturn(1);
        $address->expects($this->never())->method('getCustomer');

        $this->listener->preCreate($event);
    }

    public function testDoesNothingIfAddressDoesNotHaveACustomerAssigned(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $address = $this->createMock(AddressInterface::class);

        $event->method('getSubject')->willReturn($address);
        $address->expects($this->once())->method('getId')->willReturn(null);
        $address->expects($this->once())->method('getCustomer')->willReturn(null);

        $this->listener->preCreate($event);
    }
}
