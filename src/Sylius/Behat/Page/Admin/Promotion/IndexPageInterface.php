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

namespace Sylius\Behat\Page\Admin\Promotion;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

interface IndexPageInterface extends BaseIndexPageInterface
{
    /**
     * @return int
     */
    public function getUsageNumber(PromotionInterface $promotion);

    /**
     * @return bool
     */
    public function isAbleToManageCouponsFor(PromotionInterface $promotion);

    /**
     * @return bool
     */
    public function isCouponBasedFor(PromotionInterface $promotion);
}
