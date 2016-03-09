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
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @mixin ProductContext
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ProductContextSpec extends ObjectBehavior
{
    function let(SharedStorageInterface $sharedStorage, ProductVariantRepositoryInterface $productVariantRepository)
    {
        $this->beConstructedWith($sharedStorage, $productVariantRepository);
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
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant
    ) {
        $productVariantRepository->remove($productVariant)->shouldBeCalled();

        $this->iDeleteTheVariantOfProduct($productVariant);
    }

    function it_tries_to_delete_a_product_variant_that_should_not_be_delted_from_the_repository(
        SharedStorageInterface $sharedStorage,
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant
    ) {
        $productVariantRepository->remove($productVariant)->willThrow(DBALException::class);
        $sharedStorage->set('last_exception', Argument::type(DBALException::class))->shouldBeCalled();

        $this->iTryToDeleteTheVariantOfProduct($productVariant);
    }

    function it_checks_if_a_product_variant_exists_in_the_repository(
        SharedStorageInterface $sharedStorage,
        ProductVariantRepositoryInterface $productVariantRepository
    ) {
        $sharedStorage->get('product_variant_id')->willReturn(1);
        $productVariantRepository->find(1)->willReturn(null);

        $this->productVariantShouldNotExistInTheProductCatalog();
    }

    function it_throws_an_exception_if_a_product_variant_still_exists(
        SharedStorageInterface $sharedStorage,
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant
    ) {
        $sharedStorage->get('product_variant_id')->willReturn(1);
        $productVariantRepository->find(1)->willReturn($productVariant);

        $this->shouldThrow(NotEqualException::class)->during('productVariantShouldNotExistInTheProductCatalog', [$productVariant]);
    }

    function it_checks_if_a_proper_exception_has_been_thrown_in_the_previous_step(
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

        $this->shouldThrow(FailureException::class)->during('iShouldBeNotifiedThatThisVariantIsInUseAndCannotBeDeleted', []);
    }

    function it_throws_an_exception_if_no_exception_was_passed(
        SharedStorageInterface $sharedStorage
    ) {
        $sharedStorage->get('last_exception')->willThrow(\InvalidArgumentException::class);

        $this->shouldThrow(\InvalidArgumentException::class)->during('iShouldBeNotifiedThatThisVariantIsInUseAndCannotBeDeleted', []);
    }

    function it_checks_if_product_variant_still_exist_in_a_reposiotry(
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant
    ) {
        $productVariant->getId()->willReturn(1);
        $productVariantRepository->find(1)->willReturn($productVariant);

        $this->productVariantShouldExistInTheProductCatalog($productVariant);
    }

    function it_throws_an_exception_if_a_product_variant_does_not_exist(
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant
    ) {
        $productVariant->getId()->willReturn(1);
        $productVariantRepository->find(1)->willReturn(null);

        $this->shouldThrow(FailureException::class)->during('productVariantShouldExistInTheProductCatalog', [$productVariant]);
    }
}
