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

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\ThemeBundle\DependencyInjection\Configuration;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function it_has_default_context_service_set()
    {
        $this->assertProcessedConfigurationEquals(
            [
                [],
            ],
            ['context' => 'sylius.theme.context.settable'],
            'context'
        );
    }

    /**
     * @test
     */
    public function its_context_cannot_be_empty()
    {
        $this->assertPartialConfigurationIsInvalid(
            [
                ['']
            ],
            'context'
        );
    }

    /**
     * @test
     */
    public function its_context_can_be_overridden()
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['context' => 'sylius.theme.context.custom'],
            ],
            ['context' => 'sylius.theme.context.custom'],
            'context'
        );
    }

    /**
     * @test
     */
    public function assets_support_is_enabled_by_default()
    {
        $this->assertProcessedConfigurationEquals([[]], ['assets' => ['enabled' => true]], 'assets');
    }

    /**
     * @test
     */
    public function assets_support_may_be_toggled()
    {
        $this->assertProcessedConfigurationEquals([['assets' => ['enabled' => true]]], ['assets' => ['enabled' => true]], 'assets');
        $this->assertProcessedConfigurationEquals([['assets' => []]], ['assets' => ['enabled' => true]], 'assets');
        $this->assertProcessedConfigurationEquals([['assets' => null]], ['assets' => ['enabled' => true]], 'assets');

        $this->assertProcessedConfigurationEquals([['assets' => ['enabled' => false]]], ['assets' => ['enabled' => false]], 'assets');
        $this->assertProcessedConfigurationEquals([['assets' => false]], ['assets' => ['enabled' => false]], 'assets');
    }

    /**
     * @test
     */
    public function templating_support_is_enabled_by_default()
    {
        $this->assertProcessedConfigurationEquals([[]], ['templating' => ['enabled' => true]], 'templating');
    }

    /**
     * @test
     */
    public function templating_support_may_be_toggled()
    {
        $this->assertProcessedConfigurationEquals([['templating' => ['enabled' => true]]], ['templating' => ['enabled' => true]], 'templating');
        $this->assertProcessedConfigurationEquals([['templating' => []]], ['templating' => ['enabled' => true]], 'templating');
        $this->assertProcessedConfigurationEquals([['templating' => null]], ['templating' => ['enabled' => true]], 'templating');

        $this->assertProcessedConfigurationEquals([['templating' => ['enabled' => false]]], ['templating' => ['enabled' => false]], 'templating');
        $this->assertProcessedConfigurationEquals([['templating' => false]], ['templating' => ['enabled' => false]], 'templating');
    }

    /**
     * @test
     */
    public function translations_support_is_enabled_by_default()
    {
        $this->assertProcessedConfigurationEquals([[]], ['translations' => ['enabled' => true]], 'translations');
    }

    /**
     * @test
     */
    public function translations_support_may_be_toggled()
    {
        $this->assertProcessedConfigurationEquals([['translations' => ['enabled' => true]]], ['translations' => ['enabled' => true]], 'translations');
        $this->assertProcessedConfigurationEquals([['translations' => []]], ['translations' => ['enabled' => true]], 'translations');
        $this->assertProcessedConfigurationEquals([['translations' => null]], ['translations' => ['enabled' => true]], 'translations');

        $this->assertProcessedConfigurationEquals([['translations' => ['enabled' => false]]], ['translations' => ['enabled' => false]], 'translations');
        $this->assertProcessedConfigurationEquals([['translations' => false]], ['translations' => ['enabled' => false]], 'translations');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }
}
