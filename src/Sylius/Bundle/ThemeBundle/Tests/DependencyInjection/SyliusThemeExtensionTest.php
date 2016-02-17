<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\ThemeBundle\DependencyInjection\SyliusThemeExtension;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class SyliusThemeExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_sets_configured_theme_locations_as_parameter()
    {
        $this->load(['sources' => ['filesystem' => ['locations' => ['/my/path']]]]);

        $this->assertContainerBuilderHasParameter('sylius.theme.configuration.filesystem.locations', ['/my/path']);
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions()
    {
        return [
            new SyliusThemeExtension(),
        ];
    }
}
