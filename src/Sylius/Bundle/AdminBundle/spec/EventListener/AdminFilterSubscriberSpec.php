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

namespace spec\Sylius\Bundle\AdminBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\UiBundle\Storage\FilterStorageInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class AdminFilterSubscriberSpec extends ObjectBehavior
{
    function let(FilterStorageInterface $filterStorage): void
    {
        $this->beConstructedWith($filterStorage);
    }

    function it_subscribes_to_kernel_request_event(): void
    {
        $this::getSubscribedEvents()->shouldReturn([KernelEvents::REQUEST => 'onKernelRequest']);
    }

    function it_adds_filter_to_filter_storage(
        RequestEvent $event,
        Request $request,
        ParameterBag $attributes,
        ParameterBag $query,
        FilterStorageInterface $filterStorage,
    ): void {
        $event->isMainRequest()->willReturn(true);
        $request->getRequestFormat()->willReturn('html');

        $attributes->get('_route', '')->willReturn('sylius_admin_product_index');
        $attributes->get('_sylius', [])->willReturn(['section' => 'admin']);
        $attributes->get('_controller')->willReturn('Sylius\Bundle\AdminBundle\Controller\ProductController::indexAction');
        $request->attributes = $attributes;

        $query->all()->willReturn(['filter' => 'foo']);
        $request->query = $query;

        $filterStorage->all()->willReturn([]);

        $event->getRequest()->willReturn($request);

        $filterStorage->set(Argument::any())->shouldBeCalled();

        $this->onKernelRequest($event);
    }

    function it_does_not_add_filter_to_filter_storage_if_request_is_not_main(
        RequestEvent $event,
        Request $request,
    ): void {
        $event->isMainRequest()->willReturn(false);
        $request->getRequestFormat()->willReturn('html');

        $event->getRequest()->shouldNotBeCalled();

        $this->onKernelRequest($event);
    }

    function it_does_not_add_filter_to_filter_storage_if_request_format_is_not_html(
        RequestEvent $event,
        Request $request,
        ParameterBag $attributes,
        ParameterBag $query,
        FilterStorageInterface $filterStorage,
    ): void {
        $event->isMainRequest()->willReturn(true);
        $request->getRequestFormat()->willReturn('json');

        $attributes->get('_route', '')->willReturn('sylius_admin_product_index');
        $attributes->get('_sylius', [])->willReturn(['section' => 'admin']);
        $attributes->get('_controller')->willReturn('Sylius\Bundle\AdminBundle\Controller\ProductController::indexAction');
        $request->attributes = $attributes;

        $query->all()->willReturn(['filter' => 'foo']);
        $request->query = $query;

        $filterStorage->all()->willReturn([]);

        $event->getRequest()->willReturn($request);

        $filterStorage->set(Argument::any())->shouldNotBeCalled();

        $this->onKernelRequest($event);
    }

    function it_does_not_add_filter_to_filter_storage_if_it_is_not_an_admin_section(
        RequestEvent $event,
        Request $request,
        ParameterBag $attributes,
        ParameterBag $query,
        FilterStorageInterface $filterStorage,
    ): void {
        $event->isMainRequest()->willReturn(true);
        $request->getRequestFormat()->willReturn('json');

        $attributes->get('_route', '')->willReturn('sylius_admin_product_index');
        $attributes->get('_sylius', [])->willReturn(['section' => 'shop']);
        $attributes->get('_controller')->willReturn('Sylius\Bundle\AdminBundle\Controller\ProductController::indexAction');
        $request->attributes = $attributes;

        $query->all()->willReturn(['filter' => 'foo']);
        $request->query = $query;

        $filterStorage->all()->willReturn([]);

        $event->getRequest()->willReturn($request);

        $filterStorage->set(Argument::any())->shouldNotBeCalled();

        $this->onKernelRequest($event);
    }

    function it_does_not_add_filter_to_filter_storage_if_controller_is_null(
        RequestEvent $event,
        Request $request,
        ParameterBag $attributes,
        ParameterBag $query,
        FilterStorageInterface $filterStorage,
    ): void {
        $event->isMainRequest()->willReturn(true);
        $request->getRequestFormat()->willReturn('json');

        $attributes->get('_route', '')->willReturn('sylius_admin_product_index');
        $attributes->get('_sylius', [])->willReturn(['section' => 'shop']);
        $attributes->get('_controller')->willReturn(null);
        $request->attributes = $attributes;

        $query->all()->willReturn(['filter' => 'foo']);
        $request->query = $query;

        $filterStorage->all()->willReturn([]);

        $event->getRequest()->willReturn($request);

        $filterStorage->set(Argument::any())->shouldNotBeCalled();

        $this->onKernelRequest($event);
    }

    function it_does_not_add_filter_to_filter_storage_if_route_is_missing(
        RequestEvent $event,
        Request $request,
        ParameterBag $attributes,
        ParameterBag $query,
        FilterStorageInterface $filterStorage,
    ): void {
        $event->isMainRequest()->willReturn(true);
        $request->getRequestFormat()->willReturn('json');

        $attributes->get('_route', '')->willReturn('');
        $attributes->get('_sylius', [])->willReturn(['section' => 'shop']);
        $attributes->get('_controller')->willReturn('Sylius\Bundle\AdminBundle\Controller\ProductController::indexAction');
        $request->attributes = $attributes;

        $query->all()->willReturn(['filter' => 'foo']);
        $request->query = $query;

        $filterStorage->all()->willReturn([]);

        $event->getRequest()->willReturn($request);

        $filterStorage->set(Argument::any())->shouldNotBeCalled();

        $this->onKernelRequest($event);
    }

    function it_does_not_add_filter_to_filter_storage_if_it_is_not_a_index_resource_route(
        RequestEvent $event,
        Request $request,
        ParameterBag $attributes,
        ParameterBag $query,
        FilterStorageInterface $filterStorage,
    ): void {
        $event->isMainRequest()->willReturn(true);
        $request->getRequestFormat()->willReturn('json');

        $attributes->get('_route', '')->willReturn('sylius_admin_product_update');
        $attributes->get('_sylius', [])->willReturn(['section' => 'shop']);
        $attributes->get('_controller')->willReturn('Sylius\Bundle\AdminBundle\Controller\ProductController::indexAction');
        $request->attributes = $attributes;

        $query->all()->willReturn(['filter' => 'foo']);
        $request->query = $query;

        $filterStorage->all()->willReturn([]);

        $event->getRequest()->willReturn($request);

        $filterStorage->set(Argument::any())->shouldNotBeCalled();

        $this->onKernelRequest($event);
    }
}
