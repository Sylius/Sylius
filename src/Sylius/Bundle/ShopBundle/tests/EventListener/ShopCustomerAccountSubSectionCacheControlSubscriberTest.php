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

namespace Tests\Sylius\Bundle\ShopBundle\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\ShopBundle\EventListener\ShopCustomerAccountSubSectionCacheControlSubscriber;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopCustomerAccountSubSection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

final class ShopCustomerAccountSubSectionCacheControlSubscriberTest extends TestCase
{
    private MockObject&SectionProviderInterface $sectionProvider;

    private ShopCustomerAccountSubSectionCacheControlSubscriber $shopCustomerAccountSubSectionCacheControlSubscriber;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);

        $this->shopCustomerAccountSubSectionCacheControlSubscriber = new ShopCustomerAccountSubSectionCacheControlSubscriber($this->sectionProvider);
    }

    public function testSubscribesToKernelResponseEvent(): void
    {
        $this->assertSame(
            [KernelEvents::RESPONSE => 'setCacheControlDirectives'],
            $this->shopCustomerAccountSubSectionCacheControlSubscriber::getSubscribedEvents(),
        );
    }

    public function testAddsCacheControlDirectivesToCustomerAccountRequests(): void
    {
        /** @var HttpKernelInterface&MockObject $kernel */
        $kernel = $this->createMock(HttpKernelInterface::class);
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);
        /** @var Response&MockObject $response */
        $response = $this->createMock(Response::class);
        /** @var ResponseHeaderBag&MockObject $responseHeaderBag */
        $responseHeaderBag = $this->createMock(ResponseHeaderBag::class);
        /** @var ShopCustomerAccountSubSection&MockObject $customerAccountSubSection */
        $customerAccountSubSection = $this->createMock(ShopCustomerAccountSubSection::class);

        $this->sectionProvider->expects($this->once())->method('getSection')->willReturn($customerAccountSubSection);
        $response->headers = $responseHeaderBag;
        $event = new ResponseEvent(
            $kernel,
            $request,
            KernelInterface::MAIN_REQUEST,
            $response,
        );

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
                $this->assertSame($expectedDirective, $directive);
                $this->assertSame($expectedValue, $value);
                ++$callIndex;
            })
        ;

        $this->shopCustomerAccountSubSectionCacheControlSubscriber->setCacheControlDirectives($event);
    }

    public function testDoesNothingIfSectionIsDifferentThanCustomerAccount(): void
    {
        /** @var HttpKernelInterface&MockObject $kernel */
        $kernel = $this->createMock(HttpKernelInterface::class);
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);
        /** @var Response&MockObject $response */
        $response = $this->createMock(Response::class);
        /** @var ResponseHeaderBag&MockObject $responseHeaderBag */
        $responseHeaderBag = $this->createMock(ResponseHeaderBag::class);
        /** @var SectionInterface&MockObject $section */
        $section = $this->createMock(SectionInterface::class);
        $this->sectionProvider->expects($this->once())->method('getSection')->willReturn($section);
        $response->headers = $responseHeaderBag;
        $event = new ResponseEvent(
            $kernel,
            $request,
            KernelInterface::MAIN_REQUEST,
            $response,
        );
        $responseHeaderBag->expects($this->never())->method('addCacheControlDirective');

        $this->shopCustomerAccountSubSectionCacheControlSubscriber->setCacheControlDirectives($event);
    }
}
