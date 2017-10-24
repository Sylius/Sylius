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
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\ShippingCategoryFixture;

final class ShippingCategoryFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function shipping_categories_are_optional(): void
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    /**
     * @test
     */
    public function shipping_categories_can_be_generated_randomly(): void
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function shipping_category_code_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['code' => 'CUSTOM']]]], 'custom.*.code');
    }

    /**
     * @test
     */
    public function shipping_category_description_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['description' => 'Lorem ipsum']]]], 'custom.*.description');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): ShippingCategoryFixture
    {
        return new ShippingCategoryFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
