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
        /** @var ShopCustomerAccountSubSection&MockObject $customerAccountSubSection */
        $customerAccountSubSection = $this->createMock(ShopCustomerAccountSubSection::class);

        $response = new Response();

        $this->sectionProvider->expects($this->once())->method('getSection')->willReturn($customerAccountSubSection);
        $event = new ResponseEvent(
            $kernel,
            new Request(),
            KernelInterface::MAIN_REQUEST,
            $response,
        );

        $this->shopCustomerAccountSubSectionCacheControlSubscriber->setCacheControlDirectives($event);

        $this->assertTrue($response->headers->hasCacheControlDirective('no-cache'));
        $this->assertSame('0', $response->headers->getCacheControlDirective('max-age'));
        $this->assertTrue($response->headers->hasCacheControlDirective('must-revalidate'));
        $this->assertTrue($response->headers->hasCacheControlDirective('no-store'));
    }

    public function testDoesNothingIfSectionIsDifferentThanCustomerAccount(): void
    {
        /** @var HttpKernelInterface&MockObject $kernel */
        $kernel = $this->createMock(HttpKernelInterface::class);
        /** @var SectionInterface&MockObject $section */
        $section = $this->createMock(SectionInterface::class);

        $response = new Response();

        $this->sectionProvider->expects($this->once())->method('getSection')->willReturn($section);
        $event = new ResponseEvent(
            $kernel,
            new Request(),
            KernelInterface::MAIN_REQUEST,
            $response,
        );

        $this->shopCustomerAccountSubSectionCacheControlSubscriber->setCacheControlDirectives($event);

        $this->assertFalse($response->headers->hasCacheControlDirective('no-store'));
    }
}
