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
use Sylius\Bundle\CoreBundle\Fixture\PromotionFixture;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class PromotionFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function promotions_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    /**
     * @test
     */
    public function promotions_can_be_generated_randomly()
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function promotion_code_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['code' => 'code']]]], 'custom.*.code');
    }

    /**
     * @test
     */
    public function promotion_name_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['name' => 'name']]]], 'custom.*.name');
    }

    /**
     * @test
     */
    public function promotion_description_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['description' => 'description']]]], 'custom.*.description');
    }

    /**
     * @test
     */
    public function promotion_usage_limit_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['usage_limit' => 10]]]], 'custom.*.usage_limit');
    }

    /**
     * @test
     */
    public function promotion_coupon_based_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['coupon_based' => false]]]], 'custom.*.coupon_based');
    }

    /**
     * @test
     */
    public function promotion_exclusive_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['exclusive' => false]]]], 'custom.*.exclusive');
    }

    /**
     * @test
     */
    public function promotion_priority_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['priority' => 0]]]], 'custom.*.priority');
    }

    /**
     * @test
     */
    public function promotion_channels_are_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['channels' => ['channel_1', 'channel_2']]]]], 'custom.*.channels');
    }

    /**
     * @test
     */
    public function promotion_starts_at_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['starts_at' => '-7 day']]]], 'custom.*.starts_at');
    }

    /**
     * @test
     */
    public function promotion_ends_at_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['ends_at' => '7 day']]]], 'custom.*.ends_at');
    }

    /**
     * @test
     */
    public function promotion_rules_are_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['rules' => [[
            'type' => 'cart_quantity',
            'configuration' => [
                'count' => 5,
            ],
        ]]]]]], 'custom.*.rules');
    }

    /**
     * @test
     */
    public function promotion_actions_are_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['actions' => [[
            'type' => 'order_percentage_discount',
            'configuration' => [
                'percentage' => 20,
            ],
        ]]]]]], 'custom.*.actions');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new PromotionFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
