<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface CouponRepositoryInterface extends RepositoryInterface
{
    /**
     * @param int $codeLength
     *
     * @return int
     */
    public function countCouponsByCodeLength($codeLength);

    /**
     * @param int $promotionId
     *
     * @return QueryBuilder
     */
    public function createQueryBuilderWithPromotion($promotionId);
}
