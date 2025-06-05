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

namespace Tests\Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\PromotionFixture;

final class PromotionFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    #[Test]
    public function promotions_are_optional(): void
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    #[Test]
    public function promotions_can_be_generated_randomly(): void
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    #[Test]
    public function promotion_code_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['code' => 'code']]]], 'custom.*.code');
    }

    #[Test]
    public function promotion_name_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['name' => 'name']]]], 'custom.*.name');
    }

    #[Test]
    public function promotion_description_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['description' => 'description']]]], 'custom.*.description');
    }

    #[Test]
    public function promotion_usage_limit_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['usage_limit' => 10]]]], 'custom.*.usage_limit');
    }

    #[Test]
    public function promotion_coupon_based_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['coupon_based' => false]]]], 'custom.*.coupon_based');
    }

    #[Test]
    public function promotion_exclusive_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['exclusive' => false]]]], 'custom.*.exclusive');
    }

    #[Test]
    public function promotion_priority_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['priority' => 0]]]], 'custom.*.priority');
    }

    #[Test]
    public function promotion_channels_are_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['channels' => ['channel_1', 'channel_2']]]]], 'custom.*.channels');
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

    #[Test]
    public function promotion_starts_at_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['starts_at' => '-7 day']]]], 'custom.*.starts_at');
    }

    #[Test]
    public function promotion_ends_at_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['ends_at' => '7 day']]]], 'custom.*.ends_at');
    }

    #[Test]
    public function promotion_rules_are_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['rules' => [[
            'type' => 'cart_quantity',
            'configuration' => [
                'count' => 5,
            ],
        ]]]]]], 'custom.*.rules');
    }

    #[Test]
    public function promotion_actions_are_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['actions' => [[
            'type' => 'order_percentage_discount',
            'configuration' => [
                'percentage' => 20,
            ],
        ]]]]]], 'custom.*.actions');
    }

    protected function getConfiguration(): PromotionFixture
    {
        return new PromotionFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock(),
        );
    }
}
