<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ThemeBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\ThemeBundle\DependencyInjection\SyliusThemeExtension;

final class SyliusThemeExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_aliases_configured_theme_context_service(): void
    {
        $this->load(['context' => 'sylius.theme.context.custom']);

        $this->assertContainerBuilderHasAlias('sylius.context.theme', 'sylius.theme.context.custom');
    }

    /**
     * @test
     */
    public function it_loads_all_the_supported_features_by_default(): void
    {
        $this->load([]);

        $this->assertContainerBuilderHasService('sylius.theme.asset.assets_installer');
        $this->assertContainerBuilderHasService('sylius.theme.templating.locator');
        $this->assertContainerBuilderHasService('sylius.theme.translation.translator');
    }

    /**
     * @test
     */
    public function it_does_not_load_assets_support_if_its_disabled(): void
    {
        $this->load(['assets' => ['enabled' => false]]);

        $this->assertContainerBuilderNotHasService('sylius.theme.asset.assets_installer');
    }

    /**
     * @test
     */
    public function it_does_not_load_templating_support_if_its_disabled(): void
    {
        $this->load(['templating' => ['enabled' => false]]);

        $this->assertContainerBuilderNotHasService('sylius.theme.templating.locator');
    }

    /**
     * @test
     */
    public function it_does_not_load_translations_support_if_its_disabled(): void
    {
        $this->load(['translations' => ['enabled' => false]]);

        $this->assertContainerBuilderNotHasService('sylius.theme.translation.translator');
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions(): array
    {
        return [
            new SyliusThemeExtension(),
        ];
    }
}
