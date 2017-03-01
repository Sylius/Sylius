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
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface PromotionCouponRepositoryInterface extends RepositoryInterface
{
    /**
     * @param mixed $promotionId
     *
     * @return QueryBuilder
     */
    public function createQueryBuilderByPromotionId($promotionId);

    /**
     * @param int $codeLength
     *
     * @return int
     */
    public function countByCodeLength($codeLength);

    /**
     * @param string $code
     * @param string $promotionCode
     *
     * @return PromotionInterface
     */
    public function findOneByCodeAndPromotionCode($code, $promotionCode);
}
