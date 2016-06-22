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
use Sylius\Bundle\CoreBundle\Fixture\ShippingMethodFixture;
use Sylius\Bundle\CoreBundle\Fixture\UserFixture;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Repository\ZoneRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class UserFixtureConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function users_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'users');
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
    public function users_can_be_defined_as_array_of_strings()
    {
        $this->assertProcessedConfigurationEquals(
            [['users' => ['one@example.com', 'two@example.com']]],
            ['users' => [['email' => 'one@example.com'], ['email' => 'two@example.com']]],
            'users'
        );
    }

    /**
     * @test
     */
    public function user_email_is_required()
    {
        $this->assertPartialConfigurationIsInvalid([['users' => [null]]], 'users');
        $this->assertPartialConfigurationIsInvalid([['users' => [['email' => null]]]], 'users');

        $this->assertConfigurationIsValid([['users' => [['email' => 'john.doe@example.com']]]], 'users');
    }

    /**
     * @test
     */
    public function user_first_name_is_optional()
    {
        $this->assertConfigurationIsValid([['users' => [['first_name' => 'John']]]], 'users.*.first_name');
    }

    /**
     * @test
     */
    public function user_last_name_is_optional()
    {
        $this->assertConfigurationIsValid([['users' => [['last_name' => 'Doe']]]], 'users.*.last_name');
    }

    /**
     * @test
     */
    public function user_currency_code_is_optional()
    {
        $this->assertConfigurationIsValid([['users' => [['currency_code' => 'USD']]]], 'users.*.currency_code');
    }

    /**
     * @test
     */
    public function user_may_be_toggled()
    {
        $this->assertConfigurationIsValid([['users' => [['enabled' => false]]]], 'users.*.enabled');
    }

    /**
     * @test
     */
    public function user_password_code_is_optional()
    {
        $this->assertConfigurationIsValid([['users' => [['password' => 'I.<3.Krzysztof.Krawczyk']]]], 'users.*.password');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new UserFixture(
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock()
        );
    }
}
