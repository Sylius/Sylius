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

namespace Tests\Sylius\Bundle\AdminBundle\Twig\ErrorTemplateFinder;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Provider\LoggedInAdminUserProviderInterface;
use Sylius\Bundle\AdminBundle\SectionResolver\AdminSection;
use Sylius\Bundle\AdminBundle\Twig\ErrorTemplateFinder\ErrorTemplateFinder;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\UiBundle\Twig\ErrorTemplateFinder\ErrorTemplateFinderInterface;
use Twig\Environment;
use Twig\Loader\LoaderInterface;

final class ErrorTemplateFinderTest extends TestCase
{
    private MockObject&SectionProviderInterface $sectionProviderMock;

    private LoggedInAdminUserProviderInterface&MockObject $loggedInAdminUserProviderMock;

    private Environment&MockObject $twigMock;

    private ErrorTemplateFinder $errorTemplateFinder;

    private const TEMPLATE_PREFIX = '@SyliusAdmin/errors';

    protected function setUp(): void
    {
        $this->sectionProviderMock = $this->createMock(SectionProviderInterface::class);
        $this->loggedInAdminUserProviderMock = $this->createMock(LoggedInAdminUserProviderInterface::class);
        $this->twigMock = $this->createMock(Environment::class);
        $this->errorTemplateFinder = new ErrorTemplateFinder(
            $this->sectionProviderMock,
            $this->loggedInAdminUserProviderMock,
            $this->twigMock,
        );
    }

    public function testImplementsErrorTemplateFinderInterface(): void
    {
        $this->assertInstanceOf(ErrorTemplateFinderInterface::class, $this->errorTemplateFinder);
    }

    public function testDoesNotFindTemplateForOtherSectionsThanAdmin(): void
    {
        $sectionMock = $this->createMock(SectionInterface::class);

        $this->sectionProviderMock->expects($this->once())->method('getSection')->willReturn($sectionMock);

        $this->loggedInAdminUserProviderMock->expects($this->never())->method('hasUser');
        $this->twigMock->expects($this->never())->method('getLoader');

        $this->assertNull($this->errorTemplateFinder->findTemplate(404));
    }

    public function testDoesNotFindTemplateWhenThereIsNoAdminUser(): void
    {
        $this->sectionProviderMock->expects($this->once())
            ->method('getSection')
            ->willReturn(new AdminSection())
        ;

        $this->loggedInAdminUserProviderMock->expects($this->once())
            ->method('hasUser')
            ->willReturn(false)
        ;

        $this->twigMock->expects($this->never())->method('getLoader');

        $this->assertNull($this->errorTemplateFinder->findTemplate(404));
    }

    public function testFindsTemplateForAdmin(): void
    {
        $loaderMock = $this->createMock(LoaderInterface::class);
        $templateName = self::TEMPLATE_PREFIX . '/error404.html.twig';

        $this->sectionProviderMock->expects($this->once())
            ->method('getSection')
            ->willReturn(new AdminSection())
        ;

        $this->loggedInAdminUserProviderMock->expects($this->once())
            ->method('hasUser')
            ->willReturn(true)
        ;

        $this->twigMock->expects($this->once())->method('getLoader')->willReturn($loaderMock);
        $loaderMock->expects($this->once())->method('exists')->with($templateName)->willReturn(true);

        $this->assertSame($templateName, $this->errorTemplateFinder->findTemplate(404));
    }

    public function testReturnsNullIfNeitherTemplateCanBeFound(): void
    {
        $templateName = self::TEMPLATE_PREFIX . '/error404.html.twig';
        $fallbackTemplateName = self::TEMPLATE_PREFIX . '/error.html.twig';

        $loaderMock = $this->createMock(LoaderInterface::class);

        $this->sectionProviderMock->method('getSection')->willReturn(new AdminSection());
        $this->loggedInAdminUserProviderMock->method('hasUser')->willReturn(true);
        $this->twigMock->method('getLoader')->willReturn($loaderMock);

        $expectedTemplates = [$templateName, $fallbackTemplateName];
        $callCount = 0;

        $loaderMock
            ->expects($this->exactly(2))
            ->method('exists')
            ->willReturnCallback(function ($arg) use (&$callCount, $expectedTemplates) {
                $this->assertEquals($expectedTemplates[$callCount], $arg);
                ++$callCount;

                return false;
            })
        ;

        $result = $this->errorTemplateFinder->findTemplate(404);

        $this->assertNull($result);
    }

    public function testFindsFallbackTemplateForAdmin(): void
    {
        $templateName = self::TEMPLATE_PREFIX . '/error404.html.twig';
        $fallbackTemplateName = self::TEMPLATE_PREFIX . '/error.html.twig';

        $loaderMock = $this->createMock(LoaderInterface::class);

        $this->sectionProviderMock->method('getSection')->willReturn(new AdminSection());
        $this->loggedInAdminUserProviderMock->method('hasUser')->willReturn(true);
        $this->twigMock->method('getLoader')->willReturn($loaderMock);

        $expectedTemplates = [$templateName, $fallbackTemplateName];
        $callCount = 0;

        $loaderMock
            ->expects($this->exactly(2))
            ->method('exists')
            ->willReturnCallback(function ($arg) use (&$callCount, $expectedTemplates) {
                $this->assertEquals($expectedTemplates[$callCount], $arg);
                ++$callCount;

                return $arg === self::TEMPLATE_PREFIX . '/error.html.twig';
            })
        ;

        $result = $this->errorTemplateFinder->findTemplate(404);

        $this->assertSame($fallbackTemplateName, $result);
    }
}
