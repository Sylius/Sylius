<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Review\Calculator;

use Sylius\Component\Review\Model\ReviewableInterface;

interface ReviewableRatingCalculatorInterface
{
    /**
     * @param ReviewableInterface $reviewable
     *
     * @return float
     */
    public function calculate(ReviewableInterface $reviewable): float;
}
