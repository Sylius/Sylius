<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class ProductReviewContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

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
     * @param FactoryInterface $reviewFactory
     * @param ObjectManager $reviewManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $reviewFactory,
        ObjectManager $reviewManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->reviewFactory = $reviewFactory;
        $this->reviewManager = $reviewManager;
    }

    /**
     * @Given /^(this product) has one review$/
     */
    public function productHasAReview(ProductInterface $product)
    {
        $review = $this->reviewFactory->createNew();
        $review->setTitle('title');
        $review->setRating(5);
        $review->setReviewSubject($product);

        $product->addReview($review);

        $this->reviewManager->flush();
    }
}
