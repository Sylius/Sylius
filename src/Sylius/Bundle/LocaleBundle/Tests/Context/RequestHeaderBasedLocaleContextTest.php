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

namespace Tests\Sylius\Bundle\LocaleBundle\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\LocaleBundle\Context\RequestHeaderBasedLocaleContext;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class RequestHeaderBasedLocaleContextTest extends TestCase
{
    /** @var RequestStack&MockObject */
    private MockObject $requestStack;

    /** @var LocaleProviderInterface&MockObject */
    private MockObject $localeProvider;

    private RequestHeaderBasedLocaleContext $requestHeaderBasedLocaleContext;

    protected function setUp(): void
    {
        parent::setUp();
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->localeProvider = $this->createMock(LocaleProviderInterface::class);
        $this->requestHeaderBasedLocaleContext = new RequestHeaderBasedLocaleContext($this->requestStack, $this->localeProvider);
    }

    public function testALocaleContext(): void
    {
        self::assertInstanceOf(LocaleContextInterface::class, $this->requestHeaderBasedLocaleContext);
    }

    public function testThrowsLocaleNotFoundExceptionIfMainRequestIsNotFound(): void
    {
        $this->requestStack->expects(self::once())->method('getMainRequest')->willReturn(null);

        self::expectException(LocaleNotFoundException::class);

        $this->requestHeaderBasedLocaleContext->getLocaleCode();
    }

    public function testThrowsLocaleNotFoundExceptionIfLocaleFromMainRequestPreferredLanguageCannotBeResolved(): void
    {
        $request = new Request();
        $request->headers->set('Accept-Language', 'fr_FR');

        $this->requestStack->expects(self::once())->method('getMainRequest')->willReturn($request);

        $this->localeProvider->expects(self::once())
            ->method('getDefaultLocaleCode')
            ->willReturn('pl_PL');

        $this->localeProvider->expects(self::once())
            ->method('getAvailableLocalesCodes')
            ->willReturn(['pl_PL', 'de_DE']);

        self::expectException(LocaleNotFoundException::class);

        $this->requestHeaderBasedLocaleContext->getLocaleCode();
    }

    public function testResolvesLocaleFromMainRequestPreferredLanguageInLocaleSyntax(): void
    {
        $request = new Request();
        $request->headers->set('Accept-Language', 'de_DE');

        $this->requestStack->expects(self::once())->method('getMainRequest')->willReturn($request);

        $this->localeProvider->expects(self::once())
            ->method('getDefaultLocaleCode')
            ->willReturn('pl_PL');

        $this->localeProvider->expects(self::once())
            ->method('getAvailableLocalesCodes')
            ->willReturn(['pl_PL', 'de_DE']);

        self::assertSame('de_DE', $this->requestHeaderBasedLocaleContext->getLocaleCode());
    }

    public function testResolvesLocaleFromMainRequestPreferredLanguageInMixedCasedLanguageSyntax(): void
    {
        $request = new Request();
        $request->headers->set('Accept-Language', 'dE-De');

        $this->requestStack->expects(self::once())->method('getMainRequest')->willReturn($request);

        $this->localeProvider->expects(self::once())
            ->method('getDefaultLocaleCode')
            ->willReturn('pl_PL');

        $this->localeProvider->expects(self::once())
            ->method('getAvailableLocalesCodes')
            ->willReturn(['pl_PL', 'de_DE']);

        self::assertSame('de_DE', $this->requestHeaderBasedLocaleContext->getLocaleCode());
    }

    public function testResolvesLocaleFromMainRequestPreferredLanguageInUpperCasedLanguageSyntax(): void
    {
        $request = new Request();
        $request->headers->set('Accept-Language', 'DE-DE');

        $this->requestStack->expects(self::once())->method('getMainRequest')->willReturn($request);

        $this->localeProvider->expects(self::once())
            ->method('getDefaultLocaleCode')
            ->willReturn('pl_PL');

        $this->localeProvider->expects(self::once())
            ->method('getAvailableLocalesCodes')
            ->willReturn(['pl_PL', 'de_DE']);

        self::assertSame('de_DE', $this->requestHeaderBasedLocaleContext->getLocaleCode());
    }

    public function testResolvesLocaleFromMainRequestPreferredLanguageInLowerCasedLanguageSyntax(): void
    {
        $request = new Request();
        $request->headers->set('Accept-Language', 'de-de');

        $this->requestStack->expects(self::once())->method('getMainRequest')->willReturn($request);

        $this->localeProvider->expects(self::once())
            ->method('getDefaultLocaleCode')
            ->willReturn('pl_PL');

        $this->localeProvider->expects(self::once())
            ->method('getAvailableLocalesCodes')
            ->willReturn(['pl_PL', 'de_DE']);

        self::assertSame('de_DE', $this->requestHeaderBasedLocaleContext->getLocaleCode());
    }
}
