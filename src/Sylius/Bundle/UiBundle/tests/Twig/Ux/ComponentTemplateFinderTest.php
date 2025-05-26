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

namespace Tests\Sylius\Bundle\UiBundle\Twig\Ux;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\UiBundle\Twig\Ux\ComponentTemplateFinder;
use Symfony\UX\TwigComponent\ComponentTemplateFinderInterface;
use Twig\Loader\LoaderInterface;

final class ComponentTemplateFinderTest extends TestCase
{
    private ComponentTemplateFinderInterface&MockObject $decorated;

    private LoaderInterface&MockObject $loader;

    private ComponentTemplateFinder $componentTemplateFinder;

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(ComponentTemplateFinderInterface::class);
        $this->loader = $this->createMock(LoaderInterface::class);

        $this->componentTemplateFinder = new ComponentTemplateFinder($this->decorated, $this->loader, [
            'sylius_ui' => '@SyliusUi/components',
            'sylius_ui_shop' => '@SyliusUi/shop/components',
        ]);
    }

    public function testCallsDecoratedFinderIfNoPrefixMatches(): void
    {
        $this->decorated
            ->expects($this->once())
            ->method('findAnonymousComponentTemplate')
            ->with('sylius_ui_admin:component')
            ->willReturn('ui_admin/component.html.twig')
        ;
        $this->assertSame('ui_admin/component.html.twig', $this->componentTemplateFinder->findAnonymousComponentTemplate('sylius_ui_admin:component'));
        $this->loader->expects($this->never())->method('exists');
    }

    public function testFindsAnonymousComponentTemplate(): void
    {
        $this->loader
            ->expects($this->once())->method('exists')
            ->with('@SyliusUi/shop/components/some_component/some_sub_component.html.twig')
            ->willReturn(true)
        ;
        $this->assertSame(
            '@SyliusUi/shop/components/some_component/some_sub_component.html.twig',
            $this->componentTemplateFinder->findAnonymousComponentTemplate('sylius_ui_shop:some_component:some_sub_component'),
        );
        $this->decorated->expects($this->never())->method('findAnonymousComponentTemplate');
    }

    public function testReturnsNullIfTemplateDoesNotExist(): void
    {
        $this->loader
            ->expects($this->once())->method('exists')
            ->with('@SyliusUi/shop/components/some_component.html.twig')
            ->willReturn(false)
        ;
        $this->assertNull($this->componentTemplateFinder->findAnonymousComponentTemplate('sylius_ui_shop:some_component'));
        $this->decorated->expects($this->never())->method('findAnonymousComponentTemplate');
    }
}
