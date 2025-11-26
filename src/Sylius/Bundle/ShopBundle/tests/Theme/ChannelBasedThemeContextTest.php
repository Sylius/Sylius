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

namespace Tests\Sylius\Bundle\ShopBundle\Theme;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShopBundle\Theme\ChannelBasedThemeContext;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;

final class ChannelBasedThemeContextTest extends TestCase
{
    private ChannelContextInterface&MockObject $channelContext;

    private MockObject&ThemeRepositoryInterface $themeRepository;

    private ChannelBasedThemeContext $channelBasedThemeContext;

    protected function setUp(): void
    {
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->themeRepository = $this->createMock(ThemeRepositoryInterface::class);

        $this->channelBasedThemeContext = new ChannelBasedThemeContext(
            $this->channelContext,
            $this->themeRepository,
        );
    }

    public function testImplementsThemeContextInterface(): void
    {
        $this->assertInstanceOf(ThemeContextInterface::class, $this->channelBasedThemeContext);
    }

    public function testReturnsATheme(): void
    {
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);
        /** @var ThemeInterface&MockObject $theme */
        $theme = $this->createMock(ThemeInterface::class);

        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($channel);
        $channel->expects($this->once())->method('getThemeName')->willReturn('theme/name');
        $this->themeRepository->expects($this->once())->method('findOneByName')->with('theme/name')->willReturn($theme);

        $this->assertSame($theme, $this->channelBasedThemeContext->getTheme());
    }

    public function testReturnsNullIfChannelHasNoTheme(): void
    {
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);

        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($channel);
        $channel->expects($this->once())->method('getThemeName')->willReturn(null);
        $this->themeRepository->expects($this->never())->method('findOneByName');

        $this->assertNull($this->channelBasedThemeContext->getTheme());
    }

    public function testReturnsPreviouslyFoundTheme(): void
    {
        /** @var ThemeInterface|MockObject $theme */
        $theme = $this->createMock(ThemeInterface::class);

        $reflection = new \ReflectionObject($this->channelBasedThemeContext);
        $property = $reflection->getProperty('theme');
        $property->setAccessible(true);
        $property->setValue($this->channelBasedThemeContext, $theme);

        $this->channelContext->expects($this->never())->method('getChannel');
        $this->themeRepository->expects($this->never())->method('findOneByName');

        $this->assertSame($theme, $this->channelBasedThemeContext->getTheme());
    }

    public function testReturnsNullIfTheThemeWasNotFoundPreviously(): void
    {
        $reflection = new \ReflectionObject($this->channelBasedThemeContext);
        $property = $reflection->getProperty('theme');
        $property->setAccessible(true);
        $property->setValue($this->channelBasedThemeContext, null);

        $this->channelContext->expects($this->never())->method('getChannel');
        $this->themeRepository->expects($this->never())->method('findOneByName');

        $this->assertNull($this->channelBasedThemeContext->getTheme());
    }

    public function testReturnsNullIfThereIsNoChannel(): void
    {
        $this->channelContext
            ->expects($this->once())
            ->method('getChannel')
            ->willThrowException(new ChannelNotFoundException())
        ;

        $this->assertNull($this->channelBasedThemeContext->getTheme());
    }

    public function testReturnsNullIfAnyExceptionIsThrownDuringGettingTheChannel(): void
    {
        $this->channelContext
            ->expects($this->once())
            ->method('getChannel')
            ->willThrowException(new \Exception())
        ;

        $this->assertNull($this->channelBasedThemeContext->getTheme());
    }
}
