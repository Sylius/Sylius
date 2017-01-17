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
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\CoreBundle\Fixture\ChannelFixture;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function channels_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'custom');
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
    public function channel_code_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['code' => 'CUSTOM']]]], 'custom.*.code');
    }

    /**
     * @test
     */
    public function channel_hostname_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['hostname' => 'custom.localhost']]]], 'custom.*.hostname');
    }

    /**
     * @test
     */
    public function channel_color_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['color' => 'pink']]]], 'custom.*.color');
    }

    /**
     * @test
     */
    public function channel_may_be_toggled()
    {
        $this->assertConfigurationIsValid([['custom' => [['enabled' => false]]]], 'custom.*.enabled');
    }

    /**
     * @test
     */
    public function channel_locales_are_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['locales' => ['en_US', 'pl_PL']]]]], 'custom.*.locales');
    }

    /**
     * @test
     */
    public function channel_currencies_are_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['currencies' => ['USD', 'PLN']]]]], 'custom.*.currencies');
    }

    /**
     * @test
     */
    public function channel_contact_email_is_optional()
    {
        $this->assertConfigurationIsValid(
            [['custom' => [['contact_email' => 'contact@example.com']]]],
            'custom.*.contact_email'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ChannelFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
