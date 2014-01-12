<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Repository;

use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;

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

    /**
     * Finds all active promotions that have been applied to the specified subject
     *
     * return PromotionInterface[]
     */
    public function findAppliedOnSubject(PromotionSubjectInterface $subject);
}
