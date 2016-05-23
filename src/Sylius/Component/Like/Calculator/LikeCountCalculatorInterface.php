<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Like\Calculator;

use Sylius\Component\Like\Model\LikableInterface;

/**
 * @author Loïc Frémont <loic@mobizel.com>
 */
interface LikeCountCalculatorInterface
{
    /**
     * @param LikableInterface $likable
     *
     * @return int
     */
    public function calculateLikeCount(LikableInterface $likable);

    /**
     * @param LikableInterface $likable
     *
     * @return int
     */
    public function calculateDislikeCount(LikableInterface $likable);
}
