<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReviewBundle\Updater;

use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;

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
