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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Story;

use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\FixedDiscountPriceCalculator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\PercentageDiscountPriceCalculator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForProductScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForTaxonsScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForVariantsScopeVariantChecker;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CatalogPromotionFactoryInterface;
use Zenstruck\Foundry\Story;

final class FakeCatalogPromotionsStory extends Story implements FakeCatalogPromotionsStoryInterface
{
    public function __construct(private CatalogPromotionFactoryInterface $catalogPromotionFactory)
    {
    }

    public function build(): void
    {
        $this->catalogPromotionFactory::new()
            ->withCode('winter')
            ->withName('Winter sale')
            ->withChannels(['FASHION_WEB'])
            ->withPriority(1)
            ->withScopes([
                [
                    'type' => InForVariantsScopeVariantChecker::TYPE,
                    'configuration' => [
                        'variants' => [
                            '000F_office_grey_jeans-variant-0',
                            '000F_office_grey_jeans-variant-1',
                            '000F_office_grey_jeans-variant-2',
                        ],
                    ],
                ],
            ])
            ->withActions([
                [
                    'type' => PercentageDiscountPriceCalculator::TYPE,
                    'configuration' => [
                        'amount' => 0.5,
                    ],
                ],
            ])
            ->create()
        ;

        $this->catalogPromotionFactory::new()
            ->withCode('spring')
            ->withName('Spring sale')
            ->withChannels(['FASHION_WEB'])
            ->withPriority(3)
            ->withScopes([
                [
                    'type' => InForTaxonsScopeVariantChecker::TYPE,
                    'configuration' => [
                        'taxons' => [
                            'jeans',
                        ],
                    ],
                ],
            ])
            ->withActions([
                [
                    'type' => FixedDiscountPriceCalculator::TYPE,
                    'configuration' => [
                        'FASHION_WEB' => [
                            'amount' => 3.00,
                        ],
                    ],
                ],
            ])
            ->create()
        ;

        $this->catalogPromotionFactory::new()
            ->withCode('summer')
            ->withName('Summer sale')
            ->withChannels(['FASHION_WEB'])
            ->exclusive()
            ->withPriority(4)
            ->withScopes([
                [
                    'type' => InForVariantsScopeVariantChecker::TYPE,
                    'configuration' => [
                        'variants' => [
                            '000F_office_grey_jeans-variant-0',
                        ],
                    ],
                ],
            ])
            ->withActions([
                [
                    'type' => PercentageDiscountPriceCalculator::TYPE,
                    'configuration' => [
                        'amount' => 0.5,
                    ],
                ],
            ])
            ->create()
        ;

        $this->catalogPromotionFactory::new()
            ->withCode('autumn')
            ->withName('Autumn sale')
            ->withStartDate('2 days')
            ->withEndDate('10 days')
            ->withChannels(['FASHION_WEB'])
            ->withPriority(2)
            ->withScopes([
                [
                    'type' => InForProductScopeVariantChecker::TYPE,
                    'configuration' => [
                        'products' => [
                            'Knitted_wool_blend_green_cap',
                        ],
                    ],
                ],
            ])
            ->withActions([
                [
                    'type' => PercentageDiscountPriceCalculator::TYPE,
                    'configuration' => [
                        'amount' => 0.5,
                    ],
                ],
            ])
            ->create()
        ;
    }
}
