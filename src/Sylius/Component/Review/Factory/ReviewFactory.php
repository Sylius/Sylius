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

namespace Sylius\Component\Review\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class ReviewFactory implements ReviewFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(): ReviewInterface
    {
        return $this->factory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createForSubject(ReviewableInterface $subject): ReviewInterface
    {
        /** @var ReviewInterface $review */
        $review = $this->factory->createNew();
        $review->setReviewSubject($subject);

        return $review;
    }

    /**
     * {@inheritdoc}
     */
    public function createForSubjectWithReviewer(ReviewableInterface $subject, ?ReviewerInterface $reviewer): ReviewInterface
    {
        /** @var ReviewInterface $review */
        $review = $this->createForSubject($subject);
        $review->setAuthor($reviewer);

        return $review;
    }
}
