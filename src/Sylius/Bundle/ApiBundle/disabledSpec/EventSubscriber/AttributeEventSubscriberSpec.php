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

namespace spec\Sylius\Bundle\ApiBundle\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class AttributeEventSubscriberSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $registry): void
    {
        $this->beConstructedWith($registry);
    }

    function it_implements_event_subscriber_interface(): void
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_does_nothing_when_controller_result_is_not_an_attribute(
        ServiceRegistryInterface $registry,
        HttpKernelInterface $kernel,
        Request $request,
    ): void {
        $request->getMethod()->shouldBeCalled();
        $registry->has(Argument::any())->shouldNotBeCalled();

        $this->assignStorageType(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MAIN_REQUEST,
            new \stdClass(),
        ));
    }

    function it_does_nothing_when_attribute_has_no_type(
        ServiceRegistryInterface $registry,
        HttpKernelInterface $kernel,
        Request $request,
        AttributeInterface $attribute,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_POST);
        $attribute->getType()->willReturn(null);

        $registry->has(Argument::any())->shouldNotBeCalled();

        $this->assignStorageType(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MAIN_REQUEST,
            $attribute->getWrappedObject(),
        ));
    }

    function it_does_nothing_when_attribute_has_a_storage_type(
        ServiceRegistryInterface $registry,
        HttpKernelInterface $kernel,
        Request $request,
        AttributeInterface $attribute,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_POST);
        $attribute->getType()->willReturn('text');
        $attribute->getStorageType()->willReturn('text');

        $registry->has(Argument::any())->shouldNotBeCalled();

        $this->assignStorageType(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MAIN_REQUEST,
            $attribute->getWrappedObject(),
        ));
    }

    function it_does_nothing_when_attribute_type_is_not_registered(
        ServiceRegistryInterface $registry,
        HttpKernelInterface $kernel,
        Request $request,
        AttributeInterface $attribute,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_POST);
        $attribute->getType()->willReturn('foo');
        $attribute->getStorageType()->willReturn(null);

        $registry->has('foo')->willReturn(false);

        $this->assignStorageType(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MAIN_REQUEST,
            $attribute->getWrappedObject(),
        ));
    }

    function it_sets_storage_type_based_on_set_attribute_type(
        ServiceRegistryInterface $registry,
        HttpKernelInterface $kernel,
        Request $request,
        AttributeInterface $attribute,
        AttributeTypeInterface $attributeType,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_POST);
        $attribute->getType()->willReturn('foo');
        $attribute->getStorageType()->willReturn(null);

        $registry->has('foo')->willReturn(true);
        $registry->get('foo')->willReturn($attributeType);

        $attributeType->getStorageType()->willReturn('bar');

        $attribute->setStorageType('bar')->shouldBeCalled();

        $this->assignStorageType(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MAIN_REQUEST,
            $attribute->getWrappedObject(),
        ));
    }
}
