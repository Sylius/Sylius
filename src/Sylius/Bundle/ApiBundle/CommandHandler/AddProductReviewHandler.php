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

namespace Sylius\Bundle\ApiBundle\CommandHandler;

use Sylius\Bundle\ApiBundle\Command\AddProductReview;
use Sylius\Bundle\ApiBundle\Provider\CustomerProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/** @experimental */
final class AddProductReviewHandler implements MessageHandlerInterface
{
    /** @var FactoryInterface */
    private $productReviewFactory;

    /** @var RepositoryInterface */
    private $productReviewRepository;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var CustomerProviderInterface */
    private $customerProvider;

    public function __construct(
        FactoryInterface $productReviewFactory,
        RepositoryInterface $productReviewRepository,
        ProductRepositoryInterface $productRepository,
        CustomerProviderInterface $customerProvider
    ) {
        $this->productReviewFactory = $productReviewFactory;
        $this->productReviewRepository = $productReviewRepository;
        $this->productRepository = $productRepository;
        $this->customerProvider = $customerProvider;
    }

    public function __invoke(AddProductReview $addProductReview): ReviewInterface
    {
        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneByCode($addProductReview->productCode);

        /** @var string|null $email */
        $email = $addProductReview->email;

        if ($email === null) {
            throw new \InvalidArgumentException('Visitor should provide an email');
        }

        /** @var CustomerInterface $customer */
        $customer = $this->customerProvider->provide($email);

        /** @var ReviewInterface $review */
        $review = $this->productReviewFactory->createNew();
        $review->setTitle($addProductReview->title);
        $review->setRating($addProductReview->rating);
        $review->setComment($addProductReview->comment);
        $review->setReviewSubject($product);
        $review->setAuthor($customer);

        $product->addReview($review);

        $this->productReviewRepository->add($review);

        return $review;
    }
}
