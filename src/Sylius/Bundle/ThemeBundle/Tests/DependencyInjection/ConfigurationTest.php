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
use Sylius\Bundle\ThemeBundle\Configuration\Filesystem\FilesystemConfigurationSourceFactory;
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
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }
}
