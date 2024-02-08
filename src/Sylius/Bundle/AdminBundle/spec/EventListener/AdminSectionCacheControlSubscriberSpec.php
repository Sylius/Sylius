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
use Sylius\Bundle\AdminBundle\SectionResolver\AdminSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

final class AdminSectionCacheControlSubscriberSpec extends ObjectBehavior
{
    function let(SectionProviderInterface $sectionProvider): void
    {
        $this->beConstructedWith($sectionProvider);
    }

    function it_subscribes_to_kernel_response_event(): void
    {
        $this::getSubscribedEvents()->shouldReturn([KernelEvents::RESPONSE => 'setCacheControlDirectives']);
    }

    function it_adds_cache_control_directives_to_admin_requests(
        SectionProviderInterface $sectionProvider,
        HttpKernelInterface $kernel,
        Request $request,
        Response $response,
        ResponseHeaderBag $responseHeaderBag,
        AdminSection $adminSection,
    ): void {
        $sectionProvider->getSection()->willReturn($adminSection);

        $response->headers = $responseHeaderBag->getWrappedObject();

        $event = new ResponseEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            KernelInterface::MASTER_REQUEST,
            $response->getWrappedObject(),
        );

        $responseHeaderBag->addCacheControlDirective('no-cache', true)->shouldBeCalled();
        $responseHeaderBag->addCacheControlDirective('max-age', '0')->shouldBeCalled();
        $responseHeaderBag->addCacheControlDirective('must-revalidate', true)->shouldBeCalled();
        $responseHeaderBag->addCacheControlDirective('no-store', true)->shouldBeCalled();

        $this->setCacheControlDirectives($event);
    }

    function it_does_nothing_if_section_is_different_than_admin(
        SectionProviderInterface $sectionProvider,
        HttpKernelInterface $kernel,
        Request $request,
        Response $response,
        ResponseHeaderBag $responseHeaderBag,
        SectionInterface $section,
    ): void {
        $sectionProvider->getSection()->willReturn($section);

        $response->headers = $responseHeaderBag->getWrappedObject();

        $event = new ResponseEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            KernelInterface::MASTER_REQUEST,
            $response->getWrappedObject(),
        );

        $responseHeaderBag->addCacheControlDirective('no-cache', true)->shouldNotBeCalled();
        $responseHeaderBag->addCacheControlDirective('max-age', '0')->shouldNotBeCalled();
        $responseHeaderBag->addCacheControlDirective('must-revalidate', true)->shouldNotBeCalled();
        $responseHeaderBag->addCacheControlDirective('no-store', true)->shouldNotBeCalled();

        $this->setCacheControlDirectives($event);
    }
}
