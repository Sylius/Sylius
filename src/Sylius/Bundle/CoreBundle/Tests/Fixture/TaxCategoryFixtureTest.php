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
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\TaxCategoryFixture;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TaxCategoryFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function tax_categories_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    /**
     * @test
     */
    public function tax_categories_can_be_generated_randomly()
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function tax_category_code_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['code' => 'CUSTOM']]]], 'custom.*.code');
    }

    /**
     * @test
     */
    public function tax_category_description_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['description' => 'Lorem ipsum']]]], 'custom.*.description');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new TaxCategoryFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
