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
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\ShopUserFixture;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ShopUserFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function users_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    /**
     * @test
     */
    public function users_can_be_generated_randomly()
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function user_first_name_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['first_name' => 'John']]]], 'custom.*.first_name');
    }

    /**
     * @test
     */
    public function user_last_name_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['last_name' => 'Doe']]]], 'custom.*.last_name');
    }

    /**
     * @test
     */
    public function user_may_be_toggled()
    {
        $this->assertConfigurationIsValid([['custom' => [['enabled' => false]]]], 'custom.*.enabled');
    }

    /**
     * @test
     */
    public function user_password_code_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['password' => 'I.<3.Krzysztof.Krawczyk']]]], 'custom.*.password');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ShopUserFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
