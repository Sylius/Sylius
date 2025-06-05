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

namespace Tests\Sylius\Bundle\ShopBundle\Router;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShopBundle\Router\LocaleStrippingRouter;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RouterInterface;

final class LocaleStrippingRouterTest extends TestCase
{
    private MockObject&RouterInterface $decoratedRouter;

    private LocaleContextInterface&MockObject $localeContext;

    private LocaleStrippingRouter $localeStrippingRouter;

    protected function setUp(): void
    {
        $this->decoratedRouter = $this->createMock(RouterInterface::class);
        $this->localeContext = $this->createMock(LocaleContextInterface::class);

        $this->localeStrippingRouter = new LocaleStrippingRouter($this->decoratedRouter, $this->localeContext);
    }

    public function testASymfonyRouter(): void
    {
        $this->assertInstanceOf(RouterInterface::class, $this->localeStrippingRouter);
    }

    public function testWarmable(): void
    {
        $this->assertInstanceOf(WarmableInterface::class, $this->localeStrippingRouter);

        $this->localeStrippingRouter->warmUp('/cache/dir');
    }

    public function testDelegatesPathInfoMathingToInnerRouter(): void
    {
        $this->decoratedRouter->expects($this->once())->method('match')->with('/path/info')->willReturn(['matched' => true]);

        $this->assertSame(['matched' => true], $this->localeStrippingRouter->match('/path/info'));
    }

    public function testDelegatesRequestMatchingToInnerRouterPathInfoMatchingWhenItDoesNotImplementRequestMatcherInterface(): void
    {
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);

        $request->expects($this->once())->method('getPathInfo')->willReturn('/path/info');
        $this->decoratedRouter->expects($this->once())->method('match')->with('/path/info')->willReturn(['matched' => true]);

        $this->assertSame(['matched' => true], $this->localeStrippingRouter->matchRequest($request));
    }

    public function testDelegatesRequestMatchingToInnerRouter(): void
    {
        /** @var RouterInterface&RequestMatcherInterface&MockObject $router */
        $router = $this->createMockForIntersectionOfInterfaces([
            RouterInterface::class,
            RequestMatcherInterface::class,
        ]);
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);

        $this->localeStrippingRouter = new LocaleStrippingRouter($router, $this->localeContext);
        $router->expects($this->never())->method('match');
        $router->expects($this->once())->method('matchRequest')->with($request)->willReturn(['matched' => true]);

        $this->assertSame(['matched' => true], $this->localeStrippingRouter->matchRequest($request));
    }

    public function testStripsLocaleFromTheGeneratedUrlIfLocaleIsTheSameAsTheOneFromContext(): void
    {
        $this->localeContext->expects($this->exactly(4))->method('getLocaleCode')->willReturn('pl_PL');
        $this->decoratedRouter->expects($this->exactly(4))->method('generate')->with('route_name', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn(
                'https://generated.url/?_locale=pl_PL',
                'https://generated.url/?foo=bar&_locale=pl_PL',
                'https://generated.url/?_locale=pl_PL&foo=bar',
                'https://generated.url/?bar=foo&_locale=pl_PL&foo=bar',
            )
        ;

        $this->assertSame('https://generated.url/', $this->localeStrippingRouter->generate('route_name'));
        $this->assertSame('https://generated.url/?foo=bar', $this->localeStrippingRouter->generate('route_name'));
        $this->assertSame('https://generated.url/?foo=bar', $this->localeStrippingRouter->generate('route_name'));
        $this->assertSame('https://generated.url/?bar=foo&foo=bar', $this->localeStrippingRouter->generate('route_name'));
    }

    public function testDoesNotStripLocaleFromTheGeneratedUrlIfLocaleIsDifferentThanTheOneFromContext(): void
    {
        $this->localeContext->expects($this->once())->method('getLocaleCode')->willReturn('en_US');
        $this->decoratedRouter
            ->expects($this->once())
            ->method('generate')
            ->with('route_name', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('https://generated.url/?_locale=pl_PL')
        ;

        $this->assertSame('https://generated.url/?_locale=pl_PL', $this->localeStrippingRouter->generate('route_name'));
    }

    public function testDoesNotStirpLocaleFromTheGeneratedUrlIfThereIsNoLocaleParameter(): void
    {
        $this->decoratedRouter
            ->expects($this->once())
            ->method('generate')
            ->with('route_name', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('https://generated.url/')
        ;

        $this->assertSame('https://generated.url/', $this->localeStrippingRouter->generate('route_name'));
    }
}
