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

use Doctrine\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Fixture\CatalogPromotionRuleFixture;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;

final class CatalogPromotionRuleFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /** @test */
    public function catalog_promotions_are_optional(): void
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    /** @test */
    public function catalog_promotion_rule_type_can_be_set(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['type' => 'TYPE']]]], 'custom.*.type');
    }

    /** @test */
    public function catalog_promotion_rule_configuration_can_be_set(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['configuration' => ['product_variant_code']]]]], 'custom.*.configuration');
    }

    /** @test */
    public function catalog_promotion_rule_catalog_promotion_can_be_set(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['catalogPromotion' => 'catalog_promotion']]]], 'custom.*.catalogPromotion');
    }

    protected function getConfiguration(): CatalogPromotionRuleFixture
    {
        return new CatalogPromotionRuleFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
