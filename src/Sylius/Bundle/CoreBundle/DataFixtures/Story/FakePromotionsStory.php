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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductAttributeFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductOptionFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\PromotionFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxonFactoryInterface;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Component\Core\Promotion\Action\FixedDiscountPromotionActionCommand;
use Zenstruck\Foundry\Story;

final class FakePromotionsStory extends Story implements FakePromotionsStoryInterface
{
    public function __construct(private PromotionFactoryInterface $promotionFactory)
    {
    }

    public function build(): void
    {
        $this->promotionFactory::new()
            ->withCode('christmas')
            ->withName('Christmas')
            ->withChannels(['FASHION_WEB'])
            ->couponBased()
            ->withCoupons([
                [
                    'code' => 'CHRISTMAS_SALE',
                    'expires_at' => 'December 24',
                    'usage_limit' => 10,
                    'per_customer_usage_limit' => 1,
                ],
            ])
            ->create()
        ;

        $this->promotionFactory::new()
            ->withCode('new_year')
            ->withName('New Year')
            ->withUsageLimit(10)
            ->withPriority(2)
            ->withStartDate(new \DateTimeImmutable('-7 day'))
            ->withEndDate(new \DateTimeImmutable('-7 day'))
            ->withChannels(['FASHION_WEB'])
            ->couponBased()
            ->withRules([
                [
                    'type' => 'item_total',
                    'configuration' => [
                        'FASHION_WEB' => ['amount' => 100.00],
                    ]
                ],
            ])
            ->withActions([
                [
                    'type' => FixedDiscountPromotionActionCommand::TYPE,
                    'configuration' => [
                        'FASHION_WEB' => ['amount' => 10.00],
                    ]
                ],
            ])
            ->create()
        ;
    }
}
