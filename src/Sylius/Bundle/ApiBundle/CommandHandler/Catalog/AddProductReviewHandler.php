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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Catalog;

use Sylius\Bundle\ApiBundle\Command\Catalog\AddProductReview;
use Sylius\Bundle\ApiBundle\Exception\ProductNotFoundException;
use Sylius\Bundle\CoreBundle\Resolver\CustomerResolverInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductReviewInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final readonly class AddProductReviewHandler implements MessageHandlerInterface
{
    /**
     * @param FactoryInterface<ProductReviewInterface> $productReviewFactory
     * @param RepositoryInterface<ProductReviewInterface> $productReviewRepository
     */
    public function __construct(
        private FactoryInterface $productReviewFactory,
        private RepositoryInterface $productReviewRepository,
        private ProductRepositoryInterface $productRepository,
        private CustomerResolverInterface $customerResolver,
    ) {
    }

    public function __invoke(AddProductReview $addProductReview): ReviewInterface
    {
        /** @var ProductInterface|null $product */
        $product = $this->productRepository->findOneByCode($addProductReview->productCode);

        if ($product === null) {
            throw new ProductNotFoundException(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        /** @var string|null $email */
        $email = $addProductReview->email;

        if ($email === null) {
            throw new \InvalidArgumentException('Visitor should provide an email');
        }

        $customer = $this->customerResolver->resolve($email);

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
