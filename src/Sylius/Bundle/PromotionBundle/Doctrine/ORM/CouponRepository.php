<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Promotion\Repository\CouponRepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CouponRepository extends EntityRepository implements CouponRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createQueryBuilderWithPromotion($promotionId)
    {
        return
            $this->createQueryBuilder('c')
                ->where('c.promotion = :promotionId')
                ->setParameter('promotionId', $promotionId)
            ;
    }
}
