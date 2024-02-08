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

namespace spec\Sylius\Bundle\AdminBundle\Controller;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\RedirectHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\UiBundle\Storage\FilterStorageInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\Response;

final class RedirectHandlerSpec extends ObjectBehavior
{
    function let(
        RedirectHandlerInterface $decoratedRedirectHandler,
        FilterStorageInterface $filterStorage,
    ): void {
        $this->beConstructedWith($decoratedRedirectHandler, $filterStorage);
    }

    function it_implements_redirect_handler_interface(): void
    {
        $this->shouldImplement(RedirectHandlerInterface::class);
    }

    function it_redirects_to_resource(
        RedirectHandlerInterface $decoratedRedirectHandler,
        RequestConfiguration $configuration,
        ResourceInterface $resource,
    ): void {
        $decoratedRedirectHandler->redirectToResource($configuration, $resource)->shouldBeCalled();

        $this->redirectToResource($configuration, $resource)->shouldHaveType(Response::class);
    }

    function it_redirects_to_index(
        RedirectHandlerInterface $decoratedRedirectHandler,
        RequestConfiguration $configuration,
        ResourceInterface $resource,
        FilterStorageInterface $filterStorage,
    ): void {
        $configuration->getRedirectRoute('index')->willReturn('index');
        $configuration->getRedirectParameters($resource)->willReturn([]);
        $filterStorage->all()->willReturn(['foo' => 'bar']);

        $decoratedRedirectHandler->redirectToRoute($configuration, 'index', ['foo' => 'bar'])->shouldBeCalled();

        $this->redirectToRoute($configuration, 'index', ['foo' => 'bar'])->shouldHaveType(Response::class);
    }

    function it_redirects(
        RedirectHandlerInterface $decoratedRedirectHandler,
        RequestConfiguration $configuration,
    ): void {
        $decoratedRedirectHandler->redirect($configuration, 'http://test.com', 302)->shouldBeCalled();

        $this->redirect($configuration, 'http://test.com', 302)->shouldHaveType(Response::class);
    }

    function it_redirects_to_route(
        RedirectHandlerInterface $decoratedRedirectHandler,
        RequestConfiguration $configuration,
    ): void {
        $decoratedRedirectHandler->redirectToRoute($configuration, 'my_route', [])->shouldBeCalled();

        $this->redirectToRoute($configuration, 'my_route', [])->shouldHaveType(Response::class);
    }

    function it_redirects_to_referer(
        RedirectHandlerInterface $decoratedRedirectHandler,
        RequestConfiguration $configuration,
    ): void {
        $decoratedRedirectHandler->redirectToReferer($configuration)->shouldBeCalled();

        $this->redirectToReferer($configuration)->shouldHaveType(Response::class);
    }
}
