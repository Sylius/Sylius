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
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ReviewFactory implements ReviewFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var RepositoryInterface
     */
    private $subjectRepository;

    /**
     * @param FactoryInterface $factory
     * @param RepositoryInterface $subjectRepository
     */
    public function __construct(FactoryInterface $factory, RepositoryInterface $subjectRepository)
    {
        $this->factory = $factory;
        $this->subjectRepository = $subjectRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return $this->factory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createForSubject($subjectId)
    {
        if (null === $subject = $this->subjectRepository->find($subjectId)) {
            throw new \InvalidArgumentException(sprintf('Review subject with id "%s" does not exist.', $subjectId));
        }

        $review = $this->factory->createNew();
        $review->setReviewSubject($subject);

        return $review;
    }
}
