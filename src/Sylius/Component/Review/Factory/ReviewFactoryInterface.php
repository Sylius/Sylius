<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Review\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface ReviewFactoryInterface extends FactoryInterface
{
    /**
     * @param ReviewableInterface $subject
     *
     * @return ReviewInterface
     */
    public function createForSubject(ReviewableInterface $subject);

    /**
     * @param ReviewableInterface $subject
     * @param ReviewerInterface|null $reviewer
     *
     * @return ReviewInterface
     */
    public function createForSubjectWithReviewer(ReviewableInterface $subject, ReviewerInterface $reviewer = null);
}
