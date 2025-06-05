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

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
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

final class AddProductReviewHandlerTest extends TestCase
{
    /** @var FactoryInterface|MockObject */
    private MockObject $productReviewFactoryMock;

    /** @var RepositoryInterface|MockObject */
    private MockObject $productReviewRepositoryMock;

    /** @var ProductRepositoryInterface|MockObject */
    private MockObject $productRepositoryMock;

    /** @var CustomerResolverInterface|MockObject */
    private MockObject $customerResolverMock;

    private AddProductReviewHandler $addProductReviewHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->productReviewFactoryMock = $this->createMock(FactoryInterface::class);
        $this->productReviewRepositoryMock = $this->createMock(RepositoryInterface::class);
        $this->productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);
        $this->customerResolverMock = $this->createMock(CustomerResolverInterface::class);
        $this->addProductReviewHandler = new AddProductReviewHandler($this->productReviewFactoryMock, $this->productReviewRepositoryMock, $this->productRepositoryMock, $this->customerResolverMock);
    }

    public function testAddsProductReview(): void
    {
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        /** @var ReviewInterface|MockObject $reviewMock */
        $reviewMock = $this->createMock(ReviewInterface::class);
        $this->productRepositoryMock->expects(self::once())->method('findOneByCode')->with('winter_cap')->willReturn($productMock);
        $this->customerResolverMock->expects(self::once())->method('resolve')->with('mark@example.com')->willReturn($customerMock);
        $this->productReviewFactoryMock->expects(self::once())->method('createNew')->willReturn($reviewMock);
        $reviewMock->expects(self::once())->method('setTitle')->with('Good stuff');
        $reviewMock->expects(self::once())->method('setRating')->with(5);
        $reviewMock->expects(self::once())->method('setComment')->with('Really good stuff');
        $reviewMock->expects(self::once())->method('setReviewSubject')->with($productMock);
        $reviewMock->expects(self::once())->method('setAuthor')->with($customerMock);
        $this->productReviewRepositoryMock->add($reviewMock);
        $productMock->expects(self::once())->method('addReview')->with($reviewMock);
        $this(new AddProductReview(
            title: 'Good stuff',
            rating: 5,
            comment: 'Really good stuff',
            productCode: 'winter_cap',
            email: 'mark@example.com',
        ));
    }

    public function testThrowsAnExceptionIfEmailHasNotBeenFound(): void
    {
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        $this->productRepositoryMock->expects(self::once())->method('findOneByCode')->with('winter_cap')->willReturn($productMock);
        $this->expectException(InvalidArgumentException::class);
        $this->addProductReviewHandler->__invoke(new AddProductReview(
            title: 'Good stuff',
            rating: 5,
            comment: 'Really good stuff',
            productCode: 'winter_cap',
        ));
    }

    public function testThrowsAnExceptionIfProductHasNotBeenFound(): void
    {
        $this->productRepositoryMock->expects(self::once())->method('findOneByCode')->with('winter_cap')->willReturn(null);
        $this->expectException(ProductNotFoundException::class);
        $this->addProductReviewHandler->__invoke(new AddProductReview(
            title: 'Good stuff',
            rating: 5,
            comment: 'Really good stuff',
            productCode: 'winter_cap',
        ));
    }
}
