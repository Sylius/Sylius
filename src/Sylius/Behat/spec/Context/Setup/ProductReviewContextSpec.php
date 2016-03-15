<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class ProductReviewContextSpec extends ObjectBehavior
{
    function let(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $reviewFactory,
        RepositoryInterface $reviewRepository
    ) {
        $this->beConstructedWith($sharedStorage, $reviewFactory, $reviewRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\ProductReviewContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_creates_a_review_for_a_given_product(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $reviewFactory,
        RepositoryInterface $productReviewRepository,
        ProductInterface $product,
        ReviewInterface $review
    ) {
        $sharedStorage->get('product')->willReturn($product);

        $reviewFactory->createNew()->willReturn($review);
        $review->setTitle('title')->shouldBeCalled();
        $review->setRating(5)->shouldBeCalled();
        $review->setReviewSubject($product)->shouldBeCalled();

        $product->addReview($review)->shouldBeCalled();

        $productReviewRepository->add($review);

        $this->productHasAReview($product);
    }
}
