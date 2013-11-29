<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Repository;

use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Promotion repository interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionRepositoryInterface extends RepositoryInterface
{
    /**
     * Finds all active promotions.
     *
     * @return PromotionInterface[]
     */
    public function findActive();
}
