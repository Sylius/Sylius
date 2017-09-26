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
use Sylius\Bundle\CoreBundle\Fixture\CustomerTaxCategoryFixture;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;

final class CustomerTaxCategoryFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function customer_tax_categories_are_optional(): void
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    /**
     * @test
     */
    public function customer_tax_categories_can_be_generated_randomly(): void
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function customer_tax_category_name_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['name' => 'Retail']]]], 'custom.*.name');
    }

    /**
     * @test
     */
    public function customer_tax_category_code_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['code' => 'retail']]]], 'custom.*.code');
    }

    /**
     * @test
     */
    public function customer_tax_category_description_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['description' => 'Lorem ipsum']]]], 'custom.*.description');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): CustomerTaxCategoryFixture
    {
        return new CustomerTaxCategoryFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
