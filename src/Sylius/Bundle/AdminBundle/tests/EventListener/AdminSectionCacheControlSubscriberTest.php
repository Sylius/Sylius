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
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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

        $responseHeaderBag = $this->getMockBuilder(ResponseHeaderBag::class)
            ->onlyMethods(['addCacheControlDirective'])
            ->getMock();

        $expectedCalls = [
            ['no-cache', true],
            ['max-age', '0'],
            ['must-revalidate', true],
            ['no-store', true],
        ];

        $callIndex = 0;
        $responseHeaderBag->expects($this->exactly(4))
            ->method('addCacheControlDirective')
            ->willReturnCallback(function ($directive, $value) use (&$callIndex, $expectedCalls) {
                [$expectedDirective, $expectedValue] = $expectedCalls[$callIndex];
                TestCase::assertSame($expectedDirective, $directive);
                TestCase::assertSame($expectedValue, $value);
                ++$callIndex;
            });

        $response = $this->createMock(Response::class);
        $response->headers = $responseHeaderBag;

        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);

        $event = new ResponseEvent(
            $kernel,
            $request,
            KernelInterface::MAIN_REQUEST,
            $response,
        );

        $this->subscriber->setCacheControlDirectives($event);
    }

    public function testDoesNothingIfSectionIsDifferentThanAdmin(): void
    {
        $section = $this->createMock(SectionInterface::class);
        $this->sectionProvider->method('getSection')->willReturn($section);

        $responseHeaderBag = $this->getMockBuilder(ResponseHeaderBag::class)
            ->onlyMethods(['addCacheControlDirective'])
            ->getMock();

        $responseHeaderBag->expects($this->never())->method('addCacheControlDirective');

        $response = $this->createMock(Response::class);
        $response->headers = $responseHeaderBag;

        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);

        $event = new ResponseEvent(
            $kernel,
            $request,
            KernelInterface::MAIN_REQUEST,
            $response,
        );

        $this->subscriber->setCacheControlDirectives($event);
    }
}
