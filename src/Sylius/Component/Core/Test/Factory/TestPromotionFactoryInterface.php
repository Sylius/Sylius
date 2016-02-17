<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Test\Factory;

use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Promotion\Model\RuleInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface TestPromotionFactoryInterface
{
    /**
     * @param string $name
     *
     * @return PromotionInterface
     */
    public function createPromotion($name);

    /**
     * @param string $discount
     * @param PromotionInterface $promotion
     *
     * @return ActionInterface
     */
    public function createFixedDiscountAction($discount, PromotionInterface $promotion);
}
