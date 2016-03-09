<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Doctrine\DBAL\DBALException;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\Exception\Example\NotEqualException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Behat\Context\Domain\ProductContext;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Product\Model\VariantInterface;
use Sylius\Component\Product\Repository\VariantRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @mixin ProductContext
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class ProductContextSpec extends ObjectBehavior
{
    function let(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $productRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        RepositoryInterface $reviewRepository
    ) {
        $this->beConstructedWith($sharedStorage, $productRepository, $productVariantRepository, $reviewRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Domain\ProductContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_deletes_a_product_variant_from_the_repository(
        VariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant
    ) {
        $productVariantRepository->remove($productVariant)->shouldBeCalled();

        $this->iDeleteTheVariantOfProduct($productVariant);
    }

    function it_tries_to_delete_a_product_variant_that_should_not_be_deleted_from_the_repository(
        SharedStorageInterface $sharedStorage,
        VariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant
    ) {
        $productVariantRepository->remove($productVariant)->willThrow(DBALException::class);
        $sharedStorage->set('last_exception', Argument::type(DBALException::class))->shouldBeCalled();

        $this->iTryToDeleteTheVariantOfProduct($productVariant);
    }

    function it_checks_if_a_product_variant_exists_in_the_repository(
        SharedStorageInterface $sharedStorage,
        VariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant
    ) {
        $productVariant->getId()->willReturn(1);
        $sharedStorage->get('product_variant_id')->willReturn(1);
        $productVariantRepository->find(1)->willReturn(null);

        $this->productVariantShouldNotExistInTheProductCatalog();
    }

    function it_throws_an_exception_if_a_product_variant_still_exists(
        SharedStorageInterface $sharedStorage,
        VariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant
    ) {
        $productVariant->getId()->willReturn(1);
        $sharedStorage->get('product_variant_id')->willReturn(1);
        $productVariantRepository->find(1)->willReturn($productVariant);

        $this->shouldThrow(NotEqualException::class)->during('productVariantShouldNotExistInTheProductCatalog');
    }

    function it_checks_if_a_proper_exception_was_thrown_in_the_previous_step_of_variant_deletion(
        SharedStorageInterface $sharedStorage,
        DBALException $exception
    ) {
        $sharedStorage->get('last_exception')->willReturn($exception);

        $this->iShouldBeNotifiedThatThisVariantIsInUseAndCannotBeDeleted();
    }

    function it_throws_an_exception_if_a_wrong_exception_was_passed(
        SharedStorageInterface $sharedStorage,
        \Exception $exception
    ) {
        $sharedStorage->get('last_exception')->willReturn($exception);

        $this->shouldThrow(FailureException::class)->during('iShouldBeNotifiedThatThisVariantIsInUseAndCannotBeDeleted');
    }

    function it_throws_an_exception_if_no_exception_was_passed(SharedStorageInterface $sharedStorage)
    {
        $sharedStorage->get('last_exception')->willThrow(\InvalidArgumentException::class);

        $this->shouldThrow(\InvalidArgumentException::class)->during('iShouldBeNotifiedThatThisVariantIsInUseAndCannotBeDeleted');
    }

    function it_checks_if_product_variant_still_exist_in_a_repository(
        VariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant
    ) {
        $productVariant->getId()->willReturn(1);
        $productVariantRepository->find(1)->willReturn($productVariant);

        $this->productVariantShouldExistInTheProductCatalog($productVariant);
    }

    function it_throws_an_exception_if_a_product_variant_does_not_exist(
        VariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant
    ) {
        $productVariant->getId()->willReturn(1);
        $productVariantRepository->find(1)->willReturn(null);

        $this->shouldThrow(FailureException::class)->during('productVariantShouldExistInTheProductCatalog', [$productVariant]);
    }

    function it_deletes_a_product(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $productRepository,
        ProductInterface $product
    ) {
        $product->getId()->willReturn(1);
        $productRepository->find(1)->willReturn($product);

        $sharedStorage->set('deleted_product', $product)->shouldBeCalled();
        $productRepository->remove($product)->shouldBeCalled();

        $this->iDeleteTheProduct($product);
    }

    function it_tries_to_delete_a_product_that_does_not_exist(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $productRepository,
        ProductInterface $product
    ) {
        $product->getId()->willReturn(1);
        $productRepository->find(1)->willReturn(null);

        $sharedStorage->set('deleted_product', $product)->shouldBeCalled();
        $productRepository->remove($product)->willThrow(DBALException::class);

        $sharedStorage->set('last_exception', Argument::type(DBALException::class))->shouldBeCalled();

        $this->iDeleteTheProduct($product);
    }

    function it_checks_if_there_are_no_reviews(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $reviewRepository,
        ProductInterface $product
    ) {
        $sharedStorage->get('deleted_product')->willReturn($product);
        $reviewRepository->findBy(['reviewSubject' => $product])->willReturn([]);

        $this->thereAreNoProductReviews($product);
    }

    function it_throws_an_exception_if_reviews_of_the_product_still_exist(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $reviewRepository,
        ProductInterface $product,
        ReviewInterface $review
    ) {
        $sharedStorage->get('deleted_product')->willReturn($product);
        $reviewRepository->findBy(['reviewSubject' => $product])->willReturn([$review]);

        $this->shouldThrow(FailureException::class)->during('thereAreNoProductReviews', [$product]);
    }

    function it_checks_if_there_are_no_variants(
        SharedStorageInterface $sharedStorage,
        VariantRepositoryInterface $productVariantRepository,
        ProductInterface $product
    ) {
        $sharedStorage->get('deleted_product')->willReturn($product);
        $productVariantRepository->findBy(['object' => $product])->willReturn([]);

        $this->thereAreNoVariants($product);
    }

    function it_throws_an_exception_if_variants_of_the_product_still_exist(
        SharedStorageInterface $sharedStorage,
        VariantRepositoryInterface $productVariantRepository,
        ProductInterface $product,
        VariantInterface $variant
    ) {
        $sharedStorage->get('deleted_product')->willReturn($product);
        $productVariantRepository->findBy(['object' => $product])->willReturn([$variant]);

        $this->shouldThrow(FailureException::class)->during('thereAreNoVariants', [$product]);
    }
}
