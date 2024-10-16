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

namespace Sylius\Bundle\UiBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\UiBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /** @test */
    public function it_allows_empty_twig_ux_configuration(): void
    {
        $this->assertConfigurationIsValid([['twig_ux' => []]], 'twig_ux');
    }

    /** @test */
    public function it_allows_to_configure_anonymous_component_template_prefixes(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['twig_ux' => ['anonymous_component_template_prefixes' => ['sylius_ui' => '@SyliusUi']]],
            ],
            ['twig_ux' => ['anonymous_component_template_prefixes' => ['sylius_ui' => '@SyliusUi']]],
            'twig_ux.anonymous_component_template_prefixes',
        );
    }

    /** @test */
    public function it_allows_to_configure_live_component_tags(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['twig_ux' => ['live_component_tags' => ['ui' => ['route' => 'sylius_ui', 'method' => 'get']]]],
            ],
            ['twig_ux' => ['live_component_tags' => ['ui' => ['route' => 'sylius_ui', 'method' => 'get']]]],
            'twig_ux.live_component_tags',
        );
    }

    /** @test */
    public function it_allows_to_configure_component_default_template(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['twig_ux' => ['component_default_template' => 'components/my_default_component.html.twig']],
            ],
            ['twig_ux' => ['component_default_template' => 'components/my_default_component.html.twig']],
            'twig_ux.component_default_template',
        );
    }

    /** @test */
    public function it_uses_default_component_default_template_when_not_configured(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['twig_ux' => []],
            ],
            ['twig_ux' => ['component_default_template' => '@SyliusUi/components/default.html.twig']],
            'twig_ux.component_default_template',
        );
    }

    /** @test */
    public function it_throws_an_exception_if_live_component_tags_route_is_missing(): void
    {
        $this->assertConfigurationIsInvalid(
            [
                ['twig_ux' => ['live_component_tags' => ['ui' => ['method' => 'get']]]],
            ],
            'The "route" attribute is required for the child of "sylius_ui.twig_ux.live_component_tags".',
        );
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
