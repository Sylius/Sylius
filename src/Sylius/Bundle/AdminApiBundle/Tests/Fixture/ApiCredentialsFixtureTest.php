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
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\AdminApiBundle\Fixture\ApiClientFixture;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class OAuthCredentialsFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function oauth_credentials_can_be_generated_randomly()
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
    }

    /**
     * @test
     */
    public function oauth_credentials_can_be_created_with_custom_random_id()
    {
        $this->assertConfigurationIsValid([['custom' => [[
            'random_id' => 'totally_random',
        ]]]], 'custom.*.random_id');
    }

    /**
     * @test
     */
    public function oauth_credentials_can_be_created_with_custom_secret()
    {
        $this->assertConfigurationIsValid([['custom' => [[
            'secret' => 'threeCanKeepSecretIfTwoAreDead',
        ]]]], 'custom.*.secret');
    }

    /**
     * @test
     */
    public function oauth_credentials_can_be_created_with_grant_type()
    {
        $this->assertConfigurationIsValid([['custom' => [[
            'allowed_grant_types' => [
                'password',
            ],
        ]]]], 'custom.*.allowed_grant_types');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ApiClientFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
