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

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface CountablePromotionSubjectInterface extends PromotionSubjectInterface
{
    /**
     * @return int
     */
    public function getPromotionSubjectCount();
}
