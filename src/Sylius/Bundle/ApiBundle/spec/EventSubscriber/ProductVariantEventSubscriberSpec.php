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
use Sylius\Component\Core\Event\ProductVariantCreated;
use Sylius\Component\Core\Event\ProductVariantUpdated;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class ProductVariantEventSubscriberSpec extends ObjectBehavior
{
    function let(MessageBusInterface $eventBus): void
    {
        $this->beConstructedWith($eventBus);
    }

    function it_dispatches_product_variant_created_after_creating_product_variant(
        MessageBusInterface $eventBus,
        ProductVariantInterface $variant,
        HttpKernelInterface $kernel,
        Request $request,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_POST);

        $variant->getCode()->willReturn('MUG');

        $message = new ProductVariantCreated('MUG');
        $eventBus->dispatch($message)->willReturn(new Envelope($message))->shouldBeCalled();

        $this->postWrite(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $variant->getWrappedObject(),
        ));
    }

    function it_dispatches_product_variant_updated_after_writing_product_variant(
        MessageBusInterface $eventBus,
        ProductVariantInterface $variant,
        HttpKernelInterface $kernel,
        Request $request,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_PUT);

        $variant->getCode()->willReturn('MUG');

        $message = new ProductVariantUpdated('MUG');
        $eventBus->dispatch($message)->willReturn(new Envelope($message))->shouldBeCalled();

        $this->postWrite(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $variant->getWrappedObject(),
        ));
    }

    function it_does_nothing_after_writing_other_entity(
        MessageBusInterface $eventBus,
        HttpKernelInterface $kernel,
        Request $request,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_PUT);
        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->postWrite(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            new \stdClass(),
        ));
    }
}
