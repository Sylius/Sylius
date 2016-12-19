<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use SM\Factory\FactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;
use Sylius\Component\Review\Factory\ReviewFactoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ProductReviewExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var ReviewFactoryInterface
     */
    private $productReviewFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var FactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param ReviewFactoryInterface $productReviewFactory
     * @param ProductRepositoryInterface $productRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param FactoryInterface $stateMachineFactory
     */
    public function __construct(
        ReviewFactoryInterface $productReviewFactory,
        ProductRepositoryInterface $productRepository,
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $stateMachineFactory
    ) {
        $this->productReviewFactory = $productReviewFactory;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->stateMachineFactory = $stateMachineFactory;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ReviewInterface $productReview */
        $productReview = $this->productReviewFactory->createForSubjectWithReviewer(
            $options['product'],
            $options['author']
        );
        $productReview->setTitle($options['title']);
        $productReview->setComment($options['comment']);
        $productReview->setRating($options['rating']);
        $options['product']->addReview($productReview);

        $this->applyReviewTransition($productReview, $options['status'] ? $options['status'] : $this->getRandomStatus());

        return $productReview;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('title', function (Options $options) {
                return $this->faker->words(3, true);
            })
            ->setDefault('rating', function (Options $options) {
                return $this->faker->numberBetween(1, 5);
            })
            ->setDefault('comment', function (Options $options) {
                return $this->faker->sentences(3, true);
            })
            ->setDefault('author', LazyOption::randomOne($this->customerRepository))
            ->setNormalizer('author', LazyOption::findOneBy($this->customerRepository, 'email'))
            ->setDefault('product', LazyOption::randomOne($this->productRepository))
            ->setNormalizer('product', LazyOption::findOneBy($this->productRepository, 'code'))
            ->setDefault('status', null)
        ;
    }

    /**
     * @return string
     */
    private function getRandomStatus()
    {
        $statuses = [ReviewInterface::STATUS_NEW, ReviewInterface::STATUS_ACCEPTED, ReviewInterface::STATUS_REJECTED];

        return $statuses[(rand(0, 2))];
    }

    /**
     * @param ReviewInterface $productReview
     * @param string $targetState
     */
    private function applyReviewTransition(ReviewInterface $productReview, $targetState)
    {
        /** @var StateMachineInterface $stateMachine */
        $stateMachine = $this->stateMachineFactory->get($productReview, 'sylius_product_review');
        $transition = $stateMachine->getTransitionToState($targetState);

        if (null !== $transition) {
            $stateMachine->apply($transition);
        }
    }
}
