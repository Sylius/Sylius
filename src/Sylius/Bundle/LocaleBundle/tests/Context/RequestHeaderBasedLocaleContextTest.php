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

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\LocaleBundle\Context\RequestHeaderBasedLocaleContext;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

#[AllowMockObjectsWithoutExpectations]
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

    /** @param list<string> $availableLocales */
    #[DataProvider('provideResolvingCases')]
    public function testResolvesLocaleCode(string $acceptLanguage, string $default, array $availableLocales, string $expected): void
    {
        $request = new Request();
        $request->headers->set('Accept-Language', $acceptLanguage);

        $this->requestStack->expects(self::once())->method('getMainRequest')->willReturn($request);

        $this->localeProvider->expects(self::once())
            ->method('getDefaultLocaleCode')
            ->willReturn($default);

        $this->localeProvider->expects(self::once())
            ->method('getAvailableLocalesCodes')
            ->willReturn($availableLocales);

        self::assertSame($expected, $this->requestHeaderBasedLocaleContext->getLocaleCode());
    }

    /** @param list<string> $availableLocales */
    #[DataProvider('provideNotFoundCases')]
    public function testThrowsLocaleNotFoundExceptionForRequest(string $acceptLanguage, string $default, array $availableLocales): void
    {
        $request = new Request();
        $request->headers->set('Accept-Language', $acceptLanguage);

        $this->requestStack->expects(self::once())->method('getMainRequest')->willReturn($request);

        $this->localeProvider->expects(self::once())
            ->method('getDefaultLocaleCode')
            ->willReturn($default);

        $this->localeProvider->expects(self::once())
            ->method('getAvailableLocalesCodes')
            ->willReturn($availableLocales);

        self::expectException(LocaleNotFoundException::class);

        $this->requestHeaderBasedLocaleContext->getLocaleCode();
    }

    /** @return iterable<string, array{acceptLanguage: string, default: string, availableLocales: list<string>, expected: string}> */
    public static function provideResolvingCases(): iterable
    {
        yield 'locale syntax' => [
            'acceptLanguage' => 'de_DE',
            'default' => 'pl_PL',
            'availableLocales' => ['pl_PL', 'de_DE'],
            'expected' => 'de_DE',
        ];
        yield 'mixed-cased language syntax' => [
            'acceptLanguage' => 'dE-De',
            'default' => 'pl_PL',
            'availableLocales' => ['pl_PL', 'de_DE'],
            'expected' => 'de_DE',
        ];
        yield 'upper-cased language syntax' => [
            'acceptLanguage' => 'DE-DE',
            'default' => 'pl_PL',
            'availableLocales' => ['pl_PL', 'de_DE'],
            'expected' => 'de_DE',
        ];
        yield 'lower-cased language syntax' => [
            'acceptLanguage' => 'de-de',
            'default' => 'pl_PL',
            'availableLocales' => ['pl_PL', 'de_DE'],
            'expected' => 'de_DE',
        ];
        yield 'language prefix for macro region locale' => [
            'acceptLanguage' => 'en',
            'default' => 'en_150',
            'availableLocales' => ['en_150'],
            'expected' => 'en_150',
        ];
        yield 'language prefix for Latin America macro region locale' => [
            'acceptLanguage' => 'es',
            'default' => 'es_419',
            'availableLocales' => ['es_419'],
            'expected' => 'es_419',
        ];
        yield 'first locale by language prefix when multiple macro region locales available' => [
            'acceptLanguage' => 'en',
            'default' => 'en_001',
            'availableLocales' => ['en_001', 'en_150'],
            'expected' => 'en_001',
        ];
    }

    /** @return iterable<string, array{acceptLanguage: string, default: string, availableLocales: list<string>}> */
    public static function provideNotFoundCases(): iterable
    {
        yield 'locale not available' => [
            'acceptLanguage' => 'fr_FR',
            'default' => 'pl_PL',
            'availableLocales' => ['pl_PL', 'de_DE'],
        ];
        yield 'language prefix with no matching locale' => [
            'acceptLanguage' => 'en',
            'default' => 'it_IT',
            'availableLocales' => ['it_IT'],
        ];
    }
}
