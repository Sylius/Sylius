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

namespace Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues;

use Faker\Generator;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactoryInterface;

final class PromotionDefaultValues implements PromotionDefaultValuesInterface
{
    public function __construct(private ChannelFactoryInterface $channelFactory)
    {
    }

    public function getDefaults(Generator $faker): array
    {
        return [
            'code' => null,
            'name' => $faker->words(3, true),
            'description' => $faker->sentence(),
            'usage_limit' => null,
            'coupon_based' => false,
            'exclusive' => $faker->boolean(25),
            'priority' => 0,
            'starts_at' => null,
            'ends_at' => null,
            'channels' => $this->channelFactory::all(),
            'rules' => [],
            'coupons' => [],
            'actions' => [],
        ];
    }
}
