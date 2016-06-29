<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\ReviewBundle\Updater;

use Sylius\Review\Model\ReviewableInterface;
use Sylius\Review\Model\ReviewInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface ReviewableRatingUpdaterInterface
{
    /**
     * @param ReviewableInterface $reviewSubject
     */
    public function update(ReviewableInterface $reviewSubject);

    /**
     * @param ReviewInterface $review
     */
    public function updateFromReview(ReviewInterface $review);
}
