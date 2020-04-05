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

namespace Sylius\Bundle\AdminApiBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminApiBundle\Fixture\ApiAccessTokenFixture;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;

final class ApiAccessTokenFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function access_token_can_be_generated_randomly(): void
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
    }

    /**
     * @test
     */
    public function access_token_can_be_created_with_custom_random_id(): void
    {
        $this->assertConfigurationIsValid([['custom' => [[
            'client' => 'some_client',
        ]]]], 'custom.*.client');
    }

    /**
     * @test
     */
    public function access_token_can_be_created_with_custom_secret(): void
    {
        $this->assertConfigurationIsValid([['custom' => [[
            'user' => 'api@example.com',
        ]]]], 'custom.*.user');
    }

    /**
     * @test
     */
    public function access_token_can_be_created_with_grant_type(): void
    {
        $this->assertConfigurationIsValid([['custom' => [[
            'token' => 'some_token',
        ]]]], 'custom.*.token');
    }

    /**
     * @test
     */
    public function access_token_can_be_created_with_expires_at(): void
    {
        $this->assertConfigurationIsValid([['custom' => [[
            'expires_at' => '7 days',
        ]]]], 'custom.*.expires_at');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): ApiAccessTokenFixture
    {
        return new ApiAccessTokenFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
