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

namespace Sylius\Bundle\CoreBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\ShopUserFixture;

final class ShopUserFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function users_are_optional(): void
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    /**
     * @test
     */
    public function users_can_be_generated_randomly(): void
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function user_first_name_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['first_name' => 'John']]]], 'custom.*.first_name');
    }

    /**
     * @test
     */
    public function user_last_name_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['last_name' => 'Doe']]]], 'custom.*.last_name');
    }

    /**
     * @test
     */
    public function user_may_be_toggled(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['enabled' => false]]]], 'custom.*.enabled');
    }

    /**
     * @test
     */
    public function user_password_code_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['password' => 'I.<3.Krzysztof.Krawczyk']]]], 'custom.*.password');
    }

    /**
     * @test
     */
    public function gender_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['gender' => 'u']]]], 'custom.*.gender');
    }

    /**
     * @test
     */
    public function phone_number_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['phone_number' => '+1234567']]]], 'custom.*.phone_number');
    }

    /**
     * @test
     */
    public function birthday_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['birthday' => '01-01-2001']]]], 'custom.*.birthday');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): ShopUserFixture
    {
        return new ShopUserFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
