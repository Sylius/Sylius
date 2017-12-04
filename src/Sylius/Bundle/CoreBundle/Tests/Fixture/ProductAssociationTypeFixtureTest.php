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
use Sylius\Bundle\CoreBundle\Fixture\ProductAssociationTypeFixture;

final class ProductAssociationTypeFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function product_assoiation_types_are_optional(): void
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    /**
     * @test
     */
    public function product_association_types_can_be_generated_randomly(): void
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function product_association_type_name_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['name' => 'name']]]], 'custom.*.name');
    }

    /**
     * @test
     */
    public function product_association_type_code_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['code' => 'code']]]], 'custom.*.code');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): ProductAssociationTypeFixture
    {
        return new ProductAssociationTypeFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
