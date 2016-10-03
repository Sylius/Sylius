<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Promotion;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface IndexPageInterface extends BaseIndexPageInterface
{
    /**
     * @param PromotionInterface $promotion
     *
     * @return int
     */
    public function getUsageNumber(PromotionInterface $promotion);

    /**
     * @param PromotionInterface $promotion
     *
     * @return bool
     */
    public function isAbleToManageCouponsFor(PromotionInterface $promotion);

    /**
     * @param PromotionInterface $promotion
     *
     * @return bool
     */
    public function isCouponBasedFor(PromotionInterface $promotion);
}
