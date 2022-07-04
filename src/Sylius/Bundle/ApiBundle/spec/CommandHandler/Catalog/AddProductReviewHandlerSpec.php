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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Catalog;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Catalog\AddProductReview;
use Sylius\Bundle\ApiBundle\Provider\CustomerProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class AddProductReviewHandlerSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $productReviewFactory,
        RepositoryInterface $productReviewRepository,
        ProductRepositoryInterface $productRepository,
        CustomerProviderInterface $customerProvider,
    ): void {
        $this->beConstructedWith(
            $productReviewFactory,
            $productReviewRepository,
            $productRepository,
            $customerProvider,
        );
    }

    function it_adds_product_review(
        FactoryInterface $productReviewFactory,
        RepositoryInterface $productReviewRepository,
        ProductRepositoryInterface $productRepository,
        CustomerProviderInterface $customerProvider,
        ProductInterface $product,
        CustomerInterface $customer,
        ReviewInterface $review,
    ): void {
        $productRepository->findOneByCode('winter_cap')->willReturn($product);

        $customerProvider->provide('mark@example.com')->willReturn($customer);

        $productReviewFactory->createNew()->willReturn($review);

        $review->setTitle('Good stuff')->shouldBeCalled();
        $review->setRating(5)->shouldBeCalled();
        $review->setComment('Really good stuff')->shouldBeCalled();
        $review->setReviewSubject($product->getWrappedObject())->shouldBeCalled();
        $review->setAuthor($customer)->shouldBeCalled();

        $productReviewRepository->add($review);

        $product->addReview($review->getWrappedObject())->shouldBeCalled();

        $this(new AddProductReview(
            'Good stuff',
            5,
            'Really good stuff',
            'winter_cap',
            'mark@example.com',
        ));
    }

    function it_throws_an_exception_if_email_has_not_been_found(
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
    ): void {
        $productRepository->findOneByCode('winter_cap')->willReturn($product);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [
                new AddProductReview(
                    'Good stuff',
                    5,
                    'Really good stuff',
                    'winter_cap',
                ),
            ])
        ;
    }
}
