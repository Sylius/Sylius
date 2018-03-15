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
use Sylius\Bundle\CoreBundle\Fixture\TaxonFixture;

final class TaxonFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function taxons_are_optional(): void
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    /**
     * @test
     */
    public function taxons_can_be_generated_randomly(): void
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function taxon_code_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['code' => 'CUSTOM']]]], 'custom.*.code');
    }

    /**
     * @test
     */
    public function taxon_slug_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['slug' => 'custom']]]], 'custom.*.slug');
    }

    /**
     * @test
     */
    public function taxon_children_are_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['children' => [['name' => 'foo']]]]]], 'custom.*.children');
    }

    /**
     * @test
     */
    public function taxon_children_may_contain_nested_array(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['custom' => [['children' => [['nested' => ['key' => 'value']]]]]]],
            ['custom' => [['children' => [['nested' => ['key' => 'value']]]]]],
            'custom.*.children'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(): TaxonFixture
    {
        return new TaxonFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
