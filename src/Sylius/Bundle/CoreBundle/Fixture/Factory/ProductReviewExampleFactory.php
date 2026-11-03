<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Faker\Factory;
use Faker\Generator;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductReviewInterface;
use Sylius\Component\Core\ProductReviewTransitions;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Review\Factory\ReviewFactoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @implements ExampleFactoryInterface<ProductReviewInterface> */
class ProductReviewExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    protected Generator $faker;

    protected OptionsResolver $optionsResolver;

    /**
     * @param ReviewFactoryInterface<ReviewInterface> $productReviewFactory
     * @param ProductRepositoryInterface<ProductInterface> $productRepository
     * @param CustomerRepositoryInterface<CustomerInterface> $customerRepository
     */
    public function __construct(
        protected readonly ReviewFactoryInterface $productReviewFactory,
        protected readonly ProductRepositoryInterface $productRepository,
        protected readonly CustomerRepositoryInterface $customerRepository,
        protected readonly StateMachineInterface $stateMachine,
    ) {
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): ReviewInterface
    {
        $options = $this->optionsResolver->resolve($options);

        $productReview = $this->productReviewFactory->createForSubjectWithReviewer(
            $options['product'],
            $options['author'],
        );
        $productReview->setTitle($options['title']);
        $productReview->setComment($options['comment']);
        $productReview->setRating($options['rating']);
        $options['product']->addReview($productReview);

        $this->applyReviewTransition($productReview, $options['status'] ?: $this->getRandomStatus());

        return $productReview;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('title', fn (Options $options): string => $this->faker->words(3, true))
            ->setDefault('rating', fn (Options $options): int => $this->faker->numberBetween(1, 5))
            ->setDefault('comment', fn (Options $options): string => $this->faker->sentences(3, true))
            ->setDefault('author', LazyOption::randomOne($this->customerRepository))
            ->setNormalizer('author', LazyOption::getOneBy($this->customerRepository, 'email'))
            ->setDefault('product', LazyOption::randomOne($this->productRepository))
            ->setNormalizer('product', LazyOption::getOneBy($this->productRepository, 'code'))
            ->setDefault('status', null)
        ;
    }

    private function getRandomStatus(): string
    {
        $statuses = [ReviewInterface::STATUS_NEW, ReviewInterface::STATUS_ACCEPTED, ReviewInterface::STATUS_REJECTED];

        return $statuses[random_int(0, 2)];
    }

    private function applyReviewTransition(ReviewInterface $productReview, string $targetState): void
    {
        $transition = $this->stateMachine->getTransitionToState($productReview, ProductReviewTransitions::GRAPH, $targetState);

        if (null !== $transition) {
            $this->stateMachine->apply($productReview, ProductReviewTransitions::GRAPH, $transition);
        }
    }
}
