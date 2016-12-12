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
use Sylius\Bundle\CoreBundle\Fixture\AddressFixture;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class AddressFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function addresses_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    /**
     * @test
     */
    public function addresses_can_be_generated_randomly()
    {
        $this->assertConfigurationIsValid([['random' => 5]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function address_names_are_optional_but_cannot_be_empty()
    {
        $this->assertConfigurationIsValid([['custom' => [['first_name' => 'John']]]], 'custom.*.first_name');
        $this->assertPartialConfigurationIsInvalid([['custom' => [['first_name' => '']]]], 'custom.*.first_name');

        $this->assertConfigurationIsValid([['custom' => [['last_name' => 'Doe']]]], 'custom.*.last_name');
        $this->assertPartialConfigurationIsInvalid([['custom' => [['last_name' => '']]]], 'custom.*.last_name');
    }

    /**
     * @test
     */
    public function address_may_contain_phone_number()
    {
        $this->assertConfigurationIsValid([['custom' => [['phone_number' => '1234567890']]]], 'custom.*.phone_number');
        $this->assertConfigurationIsValid([['custom' => [['phone_number' => '']]]], 'custom.*.phone_number');
    }

    /**
     * @test
     */
    public function address_may_contain_company()
    {
        $this->assertConfigurationIsValid([['custom' => [['company' => 'test company inc.']]]], 'custom.*.company');
        $this->assertConfigurationIsValid([['custom' => [['company' => '']]]], 'custom.*.company');
    }

    /**
     * @test
     */
    public function address_street_is_optional_but_cannot_be_empty()
    {
        $this->assertConfigurationIsValid([['custom' => [['street' => 'Assert Av.']]]], 'custom.*.street');
        $this->assertPartialConfigurationIsInvalid([['custom' => [['street' => '']]]], 'custom.*.street');
    }

    /**
     * @test
     */
    public function address_city_is_optional_but_cannot_be_empty()
    {
        $this->assertConfigurationIsValid([['custom' => [['city' => 'Melbourne']]]], 'custom.*.city');
        $this->assertPartialConfigurationIsInvalid([['custom' => [['city' => '']]]], 'custom.*.city');
    }

    /**
     * @test
     */
    public function address_postcode_is_optional_but_cannot_be_empty()
    {
        $this->assertConfigurationIsValid([['custom' => [['postcode' => '01-2345']]]], 'custom.*.postcode');
        $this->assertPartialConfigurationIsInvalid([['custom' => [['postcode' => '']]]], 'custom.*.postcode');
    }

    /**
     * @test
     */
    public function address_country_code_is_optional_but_cannot_be_empty()
    {
        $this->assertConfigurationIsValid([['custom' => [['country_code' => 'UK']]]], 'custom.*.country_code');
        $this->assertPartialConfigurationIsInvalid([['custom' => [['country_code' => '']]]], 'custom.*.country_code');
    }

    /**
     * @test
     */
    public function address_province_code_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['province_code' => 'UK-YS']]]], 'custom.*.province_code');
    }

    /**
     * @test
     */
    public function address_province_name_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['province_name' => 'Yorkshire']]]], 'custom.*.province_name');
    }

    /**
     * @test
     */
    public function address_customer_is_optional_but_cannot_be_empty()
    {
        $this->assertConfigurationIsValid([['custom' => [['customer' => 'example@example.com']]]], 'custom.*.customer');
        $this->assertPartialConfigurationIsInvalid([['custom' => [['customer' => '']]]], 'custom.*.customer');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new AddressFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
