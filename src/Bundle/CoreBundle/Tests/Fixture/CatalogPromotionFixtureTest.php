<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Tests\Fixture;

use Doctrine\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Fixture\CatalogPromotionFixture;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;

final class CatalogPromotionFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /** @test */
    public function catalog_promotions_are_optional(): void
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    /** @test */
    public function catalog_promotions_can_be_generated_randomly(): void
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /** @test */
    public function catalog_promotion_code_can_be_set(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['code' => 'CODE']]]], 'custom.*.code');
    }

    /** @test */
    public function catalog_promotion_name_can_be_set(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['name' => 'Name']]]], 'custom.*.name');
    }

    /** @test */
    public function catalog_promotion_exclusiveness_can_be_set(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['exclusive' => true]]]], 'custom.*.exclusive');
    }

    /** @test */
    public function catalog_promotion_priority_can_be_set(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['priority' => 4]]]], 'custom.*.priority');
    }

    /** @test */
    public function catalog_promotion_start_date_can_be_set(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['start_date' => '2020-01-02']]]], 'custom.*.start_date');
    }

    /** @test */
    public function catalog_promotion_end_date_can_be_set(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['end_date' => '2022-01-02']]]], 'custom.*.end_date');
    }

    /** @test */
    public function catalog_promotion_description_can_be_set(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['description' => 'Description']]]], 'custom.*.description');
    }

    /** @test */
    public function catalog_promotion_channels_can_be_set(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['channels' => ['first_channel', 'second_channel']]]]], 'custom.*.channels');
        $this->assertProcessedConfigurationEquals(
            [['custom' => [['channels' => []]]]],
            ['custom' => [['channels' => []]]],
            'custom.*.channels',
        );
        $this->assertProcessedConfigurationEquals(
            [['custom' => [['channels' => null]]]],
            ['custom' => [[]]],
            'custom.*.channels',
        );
    }

    /** @test */
    public function catalog_promotion_scopes_can_be_set(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['scopes' => [['type' => 'some_scope_type', 'configuration' => ['first_variant', 'second_variant']]]]]]], 'custom.*.scopes');
    }

    /** @test */
    public function catalog_promotion_actions_can_be_set(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['actions' => [['type' => 'some_action_type', 'configuration' => ['amount' => 500]]]]]]], 'custom.*.actions');
    }

    protected function getConfiguration(): CatalogPromotionFixture
    {
        return new CatalogPromotionFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock(),
        );
    }
}
