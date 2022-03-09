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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Catalog;

use Sylius\Bundle\ApiBundle\Command\Catalog\AddProductReview;
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
    public function __construct(
        private FactoryInterface $productReviewFactory,
        private RepositoryInterface $productReviewRepository,
        private ProductRepositoryInterface $productRepository,
        private CustomerProviderInterface $customerProvider
    ) {
    }

    public function __invoke(AddProductReview $addProductReview): ReviewInterface
    {
        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneByCode($addProductReview->productCode);

        /** @var string|null $email */
        $email = $addProductReview->getEmail();

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
