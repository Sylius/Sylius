<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Event\ProductCreated;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class ProductEventSubscriberSpec extends ObjectBehavior
{
    function let(MessageBusInterface $eventBus): void
    {
        $this->beConstructedWith($eventBus);
    }

    function it_dispatches_product_created_after_creating_product(
        MessageBusInterface $eventBus,
        ProductInterface $product,
        HttpKernelInterface $kernel,
        Request $request
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_POST);

        $product->getCode()->willReturn('MUG');

        $message = new ProductCreated('MUG');
        $eventBus->dispatch($message)->willReturn(new Envelope($message))->shouldBeCalled();

        $this->postWrite(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $product->getWrappedObject()
        ));
    }

    function it_does_nothing_after_writing_other_entity(
        MessageBusInterface $eventBus,
        HttpKernelInterface $kernel,
        Request $request
    ): void {
        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->postWrite(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            new \stdClass()
        ));
    }
}
