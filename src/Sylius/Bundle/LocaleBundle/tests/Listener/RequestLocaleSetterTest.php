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

namespace Tests\Sylius\Bundle\LocaleBundle\Listener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\LocaleBundle\Listener\RequestLocaleSetter;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class RequestLocaleSetterTest extends TestCase
{
    /** @var LocaleContextInterface&MockObject */
    private MockObject $localeContext;

    /** @var LocaleProviderInterface&MockObject */
    private MockObject $localeProvider;

    private RequestLocaleSetter $requestLocaleSetter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->localeContext = $this->createMock(LocaleContextInterface::class);
        $this->localeProvider = $this->createMock(LocaleProviderInterface::class);
        $this->requestLocaleSetter = new RequestLocaleSetter($this->localeContext, $this->localeProvider);
    }

    public function testSetsLocaleAndDefaultLocaleOnRequest(): void
    {
        /** @var RequestEvent&MockObject $event */
        $event = $this->createMock(RequestEvent::class);
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);

        $event->expects(self::atLeastOnce())->method('getRequest')->willReturn($request);

        $this->localeContext->expects(self::once())->method('getLocaleCode')->willReturn('pl_PL');

        $this->localeProvider->expects(self::once())
            ->method('getDefaultLocaleCode')
            ->willReturn('en_US');

        $request->expects(self::once())->method('setLocale')->with('pl_PL');

        $request->expects(self::once())->method('setDefaultLocale')->with('en_US');

        $this->requestLocaleSetter->onKernelRequest($event);
    }
}
