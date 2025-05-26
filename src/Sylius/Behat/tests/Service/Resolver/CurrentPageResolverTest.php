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

namespace Tests\Sylius\Behat\Service\Resolver;

use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Behat\Service\Resolver\CurrentPageResolver;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

final class CurrentPageResolverTest extends TestCase
{
    private MockObject&Session $session;

    private MockObject&UrlMatcherInterface $urlMatcher;

    private CurrentPageResolver $currentPageResolver;

    protected function setUp(): void
    {
        $this->session = $this->createMock(Session::class);
        $this->urlMatcher = $this->createMock(UrlMatcherInterface::class);

        $this->currentPageResolver = new CurrentPageResolver($this->session, $this->urlMatcher);
    }

    public function testImplementsCurrentPageResolverInterface(): void
    {
        $this->assertInstanceOf(CurrentPageResolverInterface::class, $this->currentPageResolver);
    }

    public function testReturnsCurrentPageBasedOnMatchedRoute(): void
    {
        /** @var SymfonyPageInterface&MockObject $createPage */
        $createPage = $this->createMock(SymfonyPageInterface::class);
        /** @var SymfonyPageInterface&MockObject $updatePage */
        $updatePage = $this->createMock(SymfonyPageInterface::class);

        $this->session->expects($this->once())->method('getCurrentUrl')->willReturn('https://sylius.com/resource/new');
        $this->urlMatcher->expects($this->once())->method('match')->with('/resource/new')->willReturn(['_route' => 'sylius_resource_create']);
        $createPage->expects($this->once())->method('getRouteName')->willReturn('sylius_resource_create');
        $updatePage->expects($this->never())->method('getRouteName')->willReturn('sylius_resource_update');

        $this->assertSame($createPage, $this->currentPageResolver->getCurrentPageWithForm([$createPage, $updatePage]));
    }

    public function testThrowsAnExceptionIfNeitherCreateNorUpdateKeyWordHasBeenFound(): void
    {
        /** @var SymfonyPageInterface&MockObject $createPage */
        $createPage = $this->createMock(SymfonyPageInterface::class);
        /** @var SymfonyPageInterface&MockObject $updatePage */
        $updatePage = $this->createMock(SymfonyPageInterface::class);

        $this->session->expects($this->once())->method('getCurrentUrl')->willReturn('https://sylius.com/resource/show');
        $this->urlMatcher->expects($this->once())->method('match')->with('/resource/show')->willReturn(['_route' => 'sylius_resource_show']);
        $createPage->expects($this->once())->method('getRouteName')->willReturn('sylius_resource_create');
        $updatePage->expects($this->once())->method('getRouteName')->willReturn('sylius_resource_update');
        $this->expectException(LogicException::class);

        $this->currentPageResolver->getCurrentPageWithForm([$createPage, $updatePage]);
    }
}
