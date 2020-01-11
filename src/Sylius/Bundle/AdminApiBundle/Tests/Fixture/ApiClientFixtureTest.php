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
use Sylius\Bundle\AdminApiBundle\Fixture\ApiClientFixture;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;

final class ApiClientFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function oauth_credentials_can_be_generated_randomly(): void
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
    }

    /**
     * @test
     */
    public function oauth_credentials_can_be_created_with_custom_random_id(): void
    {
        $this->assertConfigurationIsValid([['custom' => [[
            'random_id' => 'totally_random',
        ]]]], 'custom.*.random_id');
    }

    /**
     * @test
     */
    public function oauth_credentials_can_be_created_with_custom_secret(): void
    {
        $this->assertConfigurationIsValid([['custom' => [[
            'secret' => 'threeCanKeepSecretIfTwoAreDead',
        ]]]], 'custom.*.secret');
    }

    /**
     * @test
     */
    public function oauth_credentials_can_be_created_with_grant_type(): void
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
    protected function getConfiguration(): ApiClientFixture
    {
        return new ApiClientFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
