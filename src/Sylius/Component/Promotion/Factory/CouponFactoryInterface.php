<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Factory;

use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CouponFactoryInterface extends FactoryInterface
{
    /**
     * @param mixed $promotionId
     *
     * @return CouponInterface
     *
     * @throws \InvalidArgumentException
     */
    public function createForPromotion($promotionId);
}
