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
use SM\Factory\FactoryInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\WinzouStateMachineAdapter;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Review\Factory\ReviewFactoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductReviewExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    private Generator $faker;

    private OptionsResolver $optionsResolver;

    public function __construct(
        private ReviewFactoryInterface $productReviewFactory,
        private ProductRepositoryInterface $productRepository,
        private CustomerRepositoryInterface $customerRepository,
        private FactoryInterface|StateMachineInterface $stateMachineFactory,
    ) {
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);

        if ($this->stateMachineFactory instanceof FactoryInterface) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.13',
                sprintf(
                    'Passing an instance of "%s" as the fourth argument is deprecated. It will accept only instances of "%s" in Sylius 2.0.',
                    FactoryInterface::class,
                    StateMachineInterface::class,
                ),
            );
        }
    }

    public function create(array $options = []): ReviewInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ReviewInterface $productReview */
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
            ->setDefault('title', function (Options $options): string {
                /** @var string $words */
                $words = $this->faker->words(3, true);

                return $words;
            })
            ->setDefault('rating', fn (Options $options): int => $this->faker->numberBetween(1, 5))
            ->setDefault('comment', function (Options $options): string {
                /** @var string $sentences */
                $sentences = $this->faker->sentences(3, true);

                return $sentences;
            })
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
        $stateMachine = $this->getStateMachine();

        $transition = $stateMachine->getTransitionToState($productReview, 'sylius_product_review', $targetState);

        if (null !== $transition) {
            $stateMachine->apply($productReview, 'sylius_product_review', $transition);
        }
    }

    private function getStateMachine(): StateMachineInterface
    {
        if ($this->stateMachineFactory instanceof FactoryInterface) {
            return new WinzouStateMachineAdapter($this->stateMachineFactory);
        }

        return $this->stateMachineFactory;
    }
}
