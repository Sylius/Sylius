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

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Exception\ResourceDeleteException;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ResourceDeleteExceptionListenerSpec extends ObjectBehavior
{
    function let(UrlGeneratorInterface $router, RequestStack $requestStack): void
    {
        $this->beConstructedWith($router, $requestStack);
    }

    function it_does_nothing_if_exception_is_not_resource_delete_exception(
        KernelInterface $kernel,
        ForeignKeyConstraintViolationException $exception,
    ): void {
        $event = new ExceptionEvent($kernel->getWrappedObject(), new Request(), HttpKernelInterface::MAIN_REQUEST, $exception->getWrappedObject());

        $this->onResourceDeleteException($event)->shouldReturn(null);
    }

    function it_does_nothing_if_request_comes_from_api(
        KernelInterface $kernel,
        Request $request,
        RequestStack $requestStack,
        UrlGeneratorInterface $router,
    ): void {
        $request->attributes = new ParameterBag(['_api_operation' => 'sylius_api_admin_product_delete']);
        $request->headers = new HeaderBag(['referer' => '/admin/product/index']);
        $exception = new ResourceDeleteException('Product');
        $event = new ExceptionEvent($kernel->getWrappedObject(), $request->getWrappedObject(), HttpKernelInterface::MAIN_REQUEST, $exception);

        $requestStack->getSession()->shouldNotBeCalled();

        $router->generate(Argument::cetera())->shouldNotBeCalled();

        $this->onResourceDeleteException($event);
    }

    function it_redirects_to_referer_if_present_and_adds_flash_message(
        KernelInterface $kernel,
        Request $request,
        RequestStack $requestStack,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $router,
        SessionInterface $session,
    ): void {
        $request->attributes = new ParameterBag(['_route' => 'sylius_admin_product_delete']);
        $request->headers = new HeaderBag(['referer' => '/admin/product/index']);
        $exception = new ResourceDeleteException('Product');
        $event = new ExceptionEvent($kernel->getWrappedObject(), $request->getWrappedObject(), HttpKernelInterface::MAIN_REQUEST, $exception);

        $requestStack->getSession()->willReturn($session);
        $session->getBag('flashes')->willReturn($flashBag);
        $flashBag->add('error', ['message' => 'sylius.resource.delete_error', 'parameters' => ['%resource%' => 'Product']])->shouldBeCalled();

        $router->generate(Argument::cetera())->shouldNotBeCalled();

        $this->onResourceDeleteException($event);
    }

    function it_redirects_to_index_when_no_referer_present_and_adds_flash_message(
        KernelInterface $kernel,
        Request $request,
        RequestStack $requestStack,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $router,
        SessionInterface $session,
    ): void {
        $request->attributes = new ParameterBag(['_route' => 'sylius_admin_product_delete']);
        $request->headers = new HeaderBag();
        $exception = new ResourceDeleteException('Product');
        $event = new ExceptionEvent($kernel->getWrappedObject(), $request->getWrappedObject(), HttpKernelInterface::MAIN_REQUEST, $exception);

        $requestStack->getSession()->willReturn($session);
        $session->getBag('flashes')->willReturn($flashBag);
        $flashBag->add('error', ['message' => 'sylius.resource.delete_error', 'parameters' => ['%resource%' => 'Product']])->shouldBeCalled();

        $router->generate('sylius_admin_product_index')->willReturn('/admin/product/index');

        $this->onResourceDeleteException($event);
    }
}
