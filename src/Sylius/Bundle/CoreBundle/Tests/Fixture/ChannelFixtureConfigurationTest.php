<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\Partial\PartialProcessor;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\CoreBundle\Fixture\ChannelFixture;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelFixtureConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function channels_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'channels');
    }

    /**
     * @test
     */
    public function channels_can_be_generated_randomly()
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function channels_can_be_defined_as_array_of_strings()
    {
        $this->assertProcessedConfigurationEquals(
            [['channels' => ['custom1', 'custom2']]],
            ['channels' => [['name' => 'custom1'], ['name' => 'custom2']]],
            'channels.*.name'
        );
    }

    /**
     * @test
     */
    public function channel_name_is_required()
    {
        $this->assertPartialConfigurationIsInvalid([['channels' => [null]]], 'channels');
        $this->assertPartialConfigurationIsInvalid([['channels' => [['name' => null]]]], 'channels');

        $this->assertConfigurationIsValid([['channels' => [['name' => 'custom1']]]], 'channels');
    }

    /**
     * @test
     */
    public function channel_code_is_optional()
    {
        $this->assertConfigurationIsValid([['channels' => [['code' => 'CUSTOM']]]], 'channels.*.code');
    }

    /**
     * @test
     */
    public function channel_hostname_is_optional()
    {
        $this->assertConfigurationIsValid([['channels' => [['hostname' => 'custom.localhost']]]], 'channels.*.hostname');
    }

    /**
     * @test
     */
    public function channel_color_is_optional()
    {
        $this->assertConfigurationIsValid([['channels' => [['color' => 'pink']]]], 'channels.*.color');
    }

    /**
     * @test
     */
    public function channel_may_be_toggled()
    {
        $this->assertConfigurationIsValid([['channels' => [['enabled' => false]]]], 'channels.*.enabled');
    }

    /**
     * @test
     */
    public function channel_locales_are_optional()
    {
        $this->assertConfigurationIsValid([['channels' => [['locales' => ['en_US', 'pl_PL']]]]], 'channels.*.locales');
    }

    /**
     * @test
     */
    public function channel_currencies_are_optional()
    {
        $this->assertConfigurationIsValid([['channels' => [['currencies' => ['USD', 'PLN']]]]], 'channels.*.currencies');
    }

    /**
     * @test
     */
    public function channel_payment_methods_are_optional()
    {
        $this->assertConfigurationIsValid([['channels' => [['payment_methods' => ['en_US', 'pl_PL']]]]], 'channels.*.payment_methods');
    }

    /**
     * @test
     */
    public function channel_shipping_methods_are_optional()
    {
        $this->assertConfigurationIsValid([['channels' => [['shipping_methods' => ['en_US', 'pl_PL']]]]], 'channels.*.shipping_methods');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ChannelFixture(
            $this->getMockBuilder(ChannelFactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock()
        );
    }
}
