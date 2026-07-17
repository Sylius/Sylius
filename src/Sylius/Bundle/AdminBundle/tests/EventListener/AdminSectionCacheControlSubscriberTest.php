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

namespace Tests\Sylius\Bundle\AdminBundle\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\EventListener\AdminSectionCacheControlSubscriber;
use Sylius\Bundle\AdminBundle\SectionResolver\AdminSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

final class AdminSectionCacheControlSubscriberTest extends TestCase
{
    private MockObject&SectionProviderInterface $sectionProvider;

    private AdminSectionCacheControlSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->subscriber = new AdminSectionCacheControlSubscriber($this->sectionProvider);
    }

    public function testSubscribesToKernelResponseEvent(): void
    {
        $this->assertSame(
            [KernelEvents::RESPONSE => 'setCacheControlDirectives'],
            AdminSectionCacheControlSubscriber::getSubscribedEvents(),
        );
    }

    public function testAddsCacheControlDirectivesToAdminRequests(): void
    {
        $adminSection = $this->createMock(AdminSection::class);
        $this->sectionProvider->method('getSection')->willReturn($adminSection);

        $response = new Response();

        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = new Request();

        $event = new ResponseEvent(
            $kernel,
            $request,
            KernelInterface::MAIN_REQUEST,
            $response,
        );

        $this->subscriber->setCacheControlDirectives($event);

        $this->assertTrue($response->headers->hasCacheControlDirective('no-cache'));
        $this->assertSame('0', $response->headers->getCacheControlDirective('max-age'));
        $this->assertTrue($response->headers->hasCacheControlDirective('must-revalidate'));
        $this->assertTrue($response->headers->hasCacheControlDirective('no-store'));
    }

    public function testDoesNothingIfSectionIsDifferentThanAdmin(): void
    {
        $section = $this->createMock(SectionInterface::class);
        $this->sectionProvider->method('getSection')->willReturn($section);

        $response = new Response();

        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = new Request();

        $event = new ResponseEvent(
            $kernel,
            $request,
            KernelInterface::MAIN_REQUEST,
            $response,
        );

        $this->subscriber->setCacheControlDirectives($event);

        $this->assertFalse($response->headers->hasCacheControlDirective('no-store'));
        $this->assertFalse($response->headers->hasCacheControlDirective('must-revalidate'));
    }
}
