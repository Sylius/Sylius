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
class LikeCountCalculator implements LikeCountCalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculateLikeCount(LikableInterface $likable)
    {
        return $this->calculate($likable, true);
    }

    /**
     * {@inheritdoc}
     */
    public function calculateDislikeCount(LikableInterface $likable)
    {
        return $this->calculate($likable, false);
    }

    /**
     * @param LikableInterface $likable
     * @param boolean $authorLike
     * 
     * @return int
     */
    protected function calculate(LikableInterface $likable, $authorLike = true)
    {
        $sum = 0;
        $likes = $likable->getLikes();

        foreach ($likes as $like) {
            if ($authorLike and $like->isAuthorLike()) {
                $sum++;
            } elseif (!$authorLike and !$like->isAuthorLike()) {
                $sum++;
            }
        }

        return $sum;
    }
}
