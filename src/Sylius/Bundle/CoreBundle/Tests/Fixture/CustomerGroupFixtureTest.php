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
use Sylius\Bundle\CoreBundle\Fixture\CustomerGroupFixture;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;

final class CustomerGroupFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function customer_groups_are_optional(): void
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    /**
     * @test
     */
    public function customer_groups_can_be_generated_randomly(): void
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function customer_group_code_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['code' => 'code']]]], 'custom.*.code');
    }

    /**
     * @test
     */
    public function customer_group_name_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['name' => 'name']]]], 'custom.*.name');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): CustomerGroupFixture
    {
        return new CustomerGroupFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
