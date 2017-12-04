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

namespace Sylius\Bundle\ReviewBundle\Updater;

use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;

interface ReviewableRatingUpdaterInterface
{
    /**
     * @param ReviewableInterface $reviewSubject
     */
    public function update(ReviewableInterface $reviewSubject): void;

    /**
     * @param ReviewInterface $review
     */
    public function updateFromReview(ReviewInterface $review): void;
}
