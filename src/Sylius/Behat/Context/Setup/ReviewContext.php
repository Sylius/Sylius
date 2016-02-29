<?php

namespace Sylius\Behat\Context\Setup;
use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class ReviewContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var RepositoryInterface
     */
    private $reviewRepository;

    /**
     * @var FactoryInterface
     */
    private $reviewFactory;

    /**
     * @var ObjectManager
     */
    private $reviewManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param RepositoryInterface $reviewRepository
     * @param FactoryInterface $reviewFactory
     * @param ObjectManager $reviewManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $reviewRepository,
        FactoryInterface $reviewFactory,
        ObjectManager $reviewManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->reviewRepository = $reviewRepository;
        $this->reviewFactory = $reviewFactory;
        $this->reviewManager = $reviewManager;
    }

    /**
     * @Given this product has one review
     */
    public function productHasAReview()
    {
        $product = $this->sharedStorage->get('product');

        $review = $this->reviewFactory->createNew();
        $review->setTitle('title');
        $review->setRating(5);
        $review->setReviewSubject($product);

        $product->addReview($review);

        $this->reviewManager->flush();
    }
}
