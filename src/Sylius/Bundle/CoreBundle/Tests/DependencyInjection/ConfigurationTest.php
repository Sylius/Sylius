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

namespace Sylius\Bundle\CoreBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /** @test */
    public function it_configures_batch_size_to_100_by_default(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['catalog_promotions' => ['batch_size' => 100]],
            'catalog_promotions',
        );
    }

    /** @test */
    public function it_allows_for_assigning_integer_as_batch_size(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['catalog_promotions' => ['batch_size' => 200]]],
            ['catalog_promotions' => ['batch_size' => 200]],
            'catalog_promotions',
        );
    }

    /** @test */
    public function it_enables_order_by_identifier_by_default(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['order_by_identifier' => true],
            'order_by_identifier',
        );
    }

    /** @test */
    public function it_allows_to_enable_order_by_identifier(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['order_by_identifier' => true]],
            ['order_by_identifier' => true],
            'order_by_identifier',
        );
    }

    /** @test */
    public function it_allows_to_disable_order_by_identifier(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['order_by_identifier' => false]],
            ['order_by_identifier' => false],
            'order_by_identifier',
        );
    }

    /** @test */
    public function it_throws_an_exception_if_value_other_then_integer_is_declared_as_batch_size(): void
    {
        $this->assertConfigurationIsInvalid([['catalog_promotions' => ['batch_size' => 'rep']]]);

        $this->assertConfigurationIsInvalid([['catalog_promotions' => ['batch_size' => 10.1]]]);
    }

    /** @test */
    public function it_throws_an_exception_if_value_of_batch_size_is_lower_then_1(): void
    {
        $this->assertConfigurationIsInvalid(
            [['catalog_promotions' => ['batch_size' => -1]]],
            'Expected value bigger than 0, but got -1.',
        );

        $this->assertConfigurationIsInvalid(
            [['catalog_promotions' => ['batch_size' => 0]]],
            ' Expected value bigger than 0, but got 0.',
        );
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
