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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\AddProductReview;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Provider\CustomerProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class AddProductReviewHandlerSpec extends ObjectBehavior
{
    function let(
        UserContextInterface $userContext,
        CustomerProviderInterface $customerProvider,
        RepositoryInterface $productReviewRepository,
        FactoryInterface $productReviewFactory,
        ProductRepositoryInterface $productRepository
    ): void {
        $this->beConstructedWith(
            $userContext,
            $customerProvider,
            $productReviewRepository,
            $productReviewFactory,
            $productRepository
        );
    }

    function it_adds_product_review_for_login_shop_user(
        UserContextInterface $userContext,
        RepositoryInterface $productReviewRepository,
        FactoryInterface $productReviewFactory,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        ReviewInterface $review
    ): void {
        $addProductReview = new AddProductReview(
            'Good stuff',
            5,
            'Really good stuff',
            'winter_cap'
        );

        $productRepository->findOneByCode($addProductReview->productCode)->willReturn($product);

        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);

        $productReviewFactory->createNew()->willReturn($review);

        $review->setTitle($addProductReview->title)->shouldBeCalled();
        $review->setRating($addProductReview->rating)->shouldBeCalled();
        $review->setComment($addProductReview->comment)->shouldBeCalled();
        $review->setReviewSubject($product->getWrappedObject())->shouldBeCalled();
        $review->setAuthor($customer)->shouldBeCalled();

        $productReviewRepository->add($review);

        $this($addProductReview);
    }

    function it_adds_product_review_for_visitor(
        UserContextInterface $userContext,
        CustomerProviderInterface $customerProvider,
        RepositoryInterface $productReviewRepository,
        FactoryInterface $productReviewFactory,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        ReviewInterface $review
    ): void {
        $addProductReview = new AddProductReview(
            'Good stuff',
            5,
            'Really good stuff',
            'winter_cap',
            'boob@example.com'
        );

        $productRepository->findOneByCode($addProductReview->productCode)->willReturn($product);

        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);

        $customerProvider->provide($addProductReview->email)->willReturn($customer);

        $productReviewFactory->createNew()->willReturn($review);

        $review->setTitle($addProductReview->title)->shouldBeCalled();
        $review->setRating($addProductReview->rating)->shouldBeCalled();
        $review->setComment($addProductReview->comment)->shouldBeCalled();
        $review->setReviewSubject($product->getWrappedObject())->shouldBeCalled();
        $review->setAuthor($customer)->shouldBeCalled();

        $productReviewRepository->add($review);

        $this($addProductReview);
    }

    function it_throws_exception_if_shop_user_has_not_been_found(
        UserContextInterface $userContext,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product
    ): void {
        $addProductReview = new AddProductReview(
            'Good stuff',
            5,
            'Really good stuff',
            'winter_cap'
        );

        $productRepository->findOneByCode($addProductReview->productCode)->willReturn($product);

        $userContext->getUser()->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$addProductReview])
        ;
    }
}
