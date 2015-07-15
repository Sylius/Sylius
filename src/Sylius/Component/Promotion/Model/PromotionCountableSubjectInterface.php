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
 * Countable promotion subject interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionCountableSubjectInterface extends PromotionSubjectInterface
{
    /**
     * Count promotion subjects.
     *
     * @return integer
     */
    public function getPromotionSubjectCount();
}
