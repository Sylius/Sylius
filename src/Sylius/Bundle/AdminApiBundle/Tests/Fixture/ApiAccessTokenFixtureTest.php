<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminApiBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\AdminApiBundle\Fixture\ApiAccessTokenFixture;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ApiAccessTokenFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function access_token_can_be_generated_randomly()
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
    }

    /**
     * @test
     */
    public function access_token_can_be_created_with_custom_random_id()
    {
        $this->assertConfigurationIsValid([['custom' => [[
            'client' => 'some_client',
        ]]]], 'custom.*.client');
    }

    /**
     * @test
     */
    public function access_token_can_be_created_with_custom_secret()
    {
        $this->assertConfigurationIsValid([['custom' => [[
            'user' => 'api@example.com'
        ]]]], 'custom.*.user');
    }

    /**
     * @test
     */
    public function access_token_can_be_created_with_grant_type()
    {
        $this->assertConfigurationIsValid([['custom' => [[
            'token' => 'some_token',
        ]]]], 'custom.*.token');
    }

    /**
     * @test
     */
    public function access_token_can_be_created_with_expires_at()
    {
        $this->assertConfigurationIsValid([['custom' => [[
            'expires_at' => '7 days',
        ]]]], 'custom.*.expires_at');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ApiAccessTokenFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
