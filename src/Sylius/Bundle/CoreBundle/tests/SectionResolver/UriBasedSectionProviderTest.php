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

namespace Tests\Sylius\Bundle\CoreBundle\SectionResolver;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionCannotBeResolvedException;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\UriBasedSectionProvider;
use Sylius\Bundle\CoreBundle\SectionResolver\UriBasedSectionResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class UriBasedSectionProviderTest extends TestCase
{
    private MockObject&RequestStack $requestStack;

    private MockObject&UriBasedSectionResolverInterface $firstSectionResolver;

    private MockObject&UriBasedSectionResolverInterface $secondSectionResolver;

    private UriBasedSectionProvider $uriBasedSectionProvider;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->firstSectionResolver = $this->createMock(UriBasedSectionResolverInterface::class);
        $this->secondSectionResolver = $this->createMock(UriBasedSectionResolverInterface::class);
        $this->uriBasedSectionProvider = new UriBasedSectionProvider($this->requestStack, [$this->firstSectionResolver, $this->secondSectionResolver]);
    }

    public function testSectionResolver(): void
    {
        $this->assertInstanceOf(SectionProviderInterface::class, $this->uriBasedSectionProvider);
    }

    public function testResolvesFirstSectionBasedOnInjectedResolvers(): void
    {
        $request = $this->createMock(Request::class);
        $section = $this->createMock(SectionInterface::class);

        $this->requestStack->expects($this->once())->method('getMainRequest')->willReturn($request);
        $request->expects($this->once())->method('getPathInfo')->willReturn('/something');
        $this->firstSectionResolver->expects($this->once())->method('getSection')->with('/something')->willReturn($section);

        $this->assertSame($section, $this->uriBasedSectionProvider->getSection());
    }

    public function testResolvesSecondSectionIfFirstWillThrowAnException(): void
    {
        $request = $this->createMock(Request::class);
        $section = $this->createMock(SectionInterface::class);

        $this->requestStack->expects($this->once())->method('getMainRequest')->willReturn($request);
        $request->expects($this->once())->method('getPathInfo')->willReturn('/something');

        $this->firstSectionResolver
            ->expects($this->once())
            ->method('getSection')
            ->with('/something')
            ->willThrowException(new SectionCannotBeResolvedException())
        ;

        $this->secondSectionResolver
            ->expects($this->once())
            ->method('getSection')
            ->with('/something')
            ->willReturn($section)
        ;

        $this->assertSame($section, $this->uriBasedSectionProvider->getSection());
    }

    public function testReturnNullIfMasterRequestHasNotBeenResolved(): void
    {
        $this->requestStack->expects($this->once())->method('getMainRequest')->willReturn(null);
        $this->assertNull($this->uriBasedSectionProvider->getSection());
    }
}
