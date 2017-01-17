<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Remover;

use Sylius\Component\Review\Model\ReviewerInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface ReviewerReviewsRemoverInterface
{
    /**
     * @param ReviewerInterface $author
     */
    public function removeReviewerReviews(ReviewerInterface $author);
}
