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

namespace Tests\Sylius\Bundle\ShopBundle\Twig\ErrorTemplateFinder;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopSection;
use Sylius\Bundle\ShopBundle\Twig\ErrorTemplateFinder\ErrorTemplateFinder;
use Sylius\Bundle\UiBundle\Twig\ErrorTemplateFinder\ErrorTemplateFinderInterface;
use Twig\Environment;
use Twig\Loader\LoaderInterface;

final class ErrorTemplateFinderTest extends TestCase
{
    private MockObject&SectionProviderInterface $sectionProvider;

    private Environment&MockObject $twig;

    private ErrorTemplateFinder $errorTemplateFinder;

    private const TEMPLATE_PREFIX = '@SyliusShop/errors';

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->twig = $this->createMock(Environment::class);

        $this->errorTemplateFinder = new ErrorTemplateFinder($this->sectionProvider, $this->twig);
    }

    public function testImplementsErrorTemplateFinderInterface(): void
    {
        $this->assertInstanceOf(ErrorTemplateFinderInterface::class, $this->errorTemplateFinder);
    }

    public function testDoesNotFindTemplateForOtherSectionsThanShop(): void
    {
        /** @var SectionInterface&MockObject $section */
        $section = $this->createMock(SectionInterface::class);

        $this->sectionProvider->expects($this->once())->method('getSection')->willReturn($section);
        $this->twig->expects($this->never())->method('getLoader');

        $this->assertNull($this->errorTemplateFinder->findTemplate(404));
    }

    public function testFindsTemplateForShop(): void
    {
        /** @var LoaderInterface&MockObject $loader */
        $loader = $this->createMock(LoaderInterface::class);

        $templateName = self::TEMPLATE_PREFIX . '/error404.html.twig';
        $this->sectionProvider->expects($this->once())->method('getSection')->willReturn(new ShopSection());
        $this->twig->expects($this->once())->method('getLoader')->willReturn($loader);
        $loader->expects($this->once())->method('exists')->with($templateName)->willReturn(true);

        $this->assertSame($templateName, $this->errorTemplateFinder->findTemplate(404));
    }

    public function testReturnsNullIfNeitherTemplateCanBeFound(): void
    {
        /** @var LoaderInterface&MockObject $loader */
        $loader = $this->createMock(LoaderInterface::class);

        $templateName = self::TEMPLATE_PREFIX . '/error404.html.twig';
        $fallbackTemplateName = self::TEMPLATE_PREFIX . '/error.html.twig';
        $this->sectionProvider->expects($this->once())->method('getSection')->willReturn(new ShopSection());
        $this->twig->expects($this->once())->method('getLoader')->willReturn($loader);
        $loader->expects($this->exactly(2))->method('exists')->willReturnMap([[$templateName, false], [$fallbackTemplateName, false]]);

        $this->assertNull($this->errorTemplateFinder->findTemplate(404));
    }

    public function testFindsFallbackTemplateForShop(): void
    {
        /** @var LoaderInterface&MockObject $loader */
        $loader = $this->createMock(LoaderInterface::class);

        $templateName = self::TEMPLATE_PREFIX . '/error404.html.twig';
        $fallbackTemplateName = self::TEMPLATE_PREFIX . '/error.html.twig';
        $this->sectionProvider->expects($this->once())->method('getSection')->willReturn(new ShopSection());
        $this->twig->expects($this->once())->method('getLoader')->willReturn($loader);
        $loader->expects($this->exactly(2))->method('exists')->willReturnMap([[$templateName, false], [$fallbackTemplateName, true]]);

        $this->assertSame($fallbackTemplateName, $this->errorTemplateFinder->findTemplate(404));
    }
}
