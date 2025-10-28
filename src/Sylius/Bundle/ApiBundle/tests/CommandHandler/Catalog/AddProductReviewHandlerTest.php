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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler\Catalog;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Catalog\AddProductReview;
use Sylius\Bundle\ApiBundle\CommandHandler\Catalog\AddProductReviewHandler;
use Sylius\Bundle\ApiBundle\Exception\ProductNotFoundException;
use Sylius\Bundle\CoreBundle\Resolver\CustomerResolverInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class AddProductReviewHandlerTest extends TestCase
{
    private FactoryInterface&MockObject $productReviewFactory;

    private MockObject&RepositoryInterface $productReviewRepository;

    private MockObject&ProductRepositoryInterface $productRepository;

    private CustomerResolverInterface&MockObject $customerResolver;

    private AddProductReviewHandler $handler;

    private MockObject&ProductInterface $product;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productReviewFactory = $this->createMock(FactoryInterface::class);
        $this->productReviewRepository = $this->createMock(RepositoryInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->customerResolver = $this->createMock(CustomerResolverInterface::class);
        $this->handler = new AddProductReviewHandler(
            $this->productReviewFactory,
            $this->productReviewRepository,
            $this->productRepository,
            $this->customerResolver,
        );
        $this->product = $this->createMock(ProductInterface::class);
    }

    public function testAddsProductReview(): void
    {
        /** @var CustomerInterface|MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        /** @var ReviewInterface|MockObject $review */
        $review = $this->createMock(ReviewInterface::class);
        $this->productRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('winter_cap')
            ->willReturn($this->product);
        $this->customerResolver->expects(self::once())
            ->method('resolve')
            ->with('mark@example.com')
            ->willReturn($customer);
        $this->productReviewFactory->expects(self::once())->method('createNew')->willReturn($review);
        $review->expects(self::once())->method('setTitle')->with('Good stuff');
        $review->expects(self::once())->method('setRating')->with(5);
        $review->expects(self::once())->method('setComment')->with('Really good stuff');
        $review->expects(self::once())->method('setReviewSubject')->with($this->product);
        $review->expects(self::once())->method('setAuthor')->with($customer);
        $this->productReviewRepository->add($review);
        $this->product->expects(self::once())->method('addReview')->with($review);
        $this->handler->__invoke(new AddProductReview(
            title: 'Good stuff',
            rating: 5,
            comment: 'Really good stuff',
            productCode: 'winter_cap',
            email: 'mark@example.com',
        ));
    }

    public function testThrowsAnExceptionIfEmailHasNotBeenFound(): void
    {
        $this->productRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('winter_cap')
            ->willReturn($this->product);
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke(new AddProductReview(
            title: 'Good stuff',
            rating: 5,
            comment: 'Really good stuff',
            productCode: 'winter_cap',
        ));
    }

    public function testThrowsAnExceptionIfProductHasNotBeenFound(): void
    {
        $this->productRepository->expects(self::once())
            ->method('findOneByCode')
            ->with('winter_cap')
            ->willReturn(null);
        self::expectException(ProductNotFoundException::class);
        $this->handler->__invoke(new AddProductReview(
            title: 'Good stuff',
            rating: 5,
            comment: 'Really good stuff',
            productCode: 'winter_cap',
        ));
    }
}
