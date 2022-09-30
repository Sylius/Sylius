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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Updater;

use SM\Factory\FactoryInterface;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class ProductReviewUpdater implements ProductReviewUpdaterInterface
{
    public function __construct(private FactoryInterface $stateMachineFactory)
    {
    }

    public function update(ReviewInterface $productReview, array $attributes): void
    {
        $productReview->setTitle($attributes['title']);
        $productReview->setRating($attributes['rating']);
        $productReview->setComment($attributes['comment']);

        $this->applyReviewTransition($productReview, $attributes['status'] ?: $this->getRandomStatus());
    }

    private function getRandomStatus(): string
    {
        $statuses = [ReviewInterface::STATUS_NEW, ReviewInterface::STATUS_ACCEPTED, ReviewInterface::STATUS_REJECTED];

        return $statuses[random_int(0, 2)];
    }

    private function applyReviewTransition(ReviewInterface $productReview, string $targetState): void
    {
        /** @var StateMachineInterface $stateMachine */
        $stateMachine = $this->stateMachineFactory->get($productReview, 'sylius_product_review');
        $transition = $stateMachine->getTransitionToState($targetState);

        if (null !== $transition) {
            $stateMachine->apply($transition);
        }
    }
}
