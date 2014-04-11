<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Model;

interface PromotionTotalAwareInterface
{
    /**
     * Get the promotion total.
     *
     * @return float
     */
    public function getPromotionTotal();
}
