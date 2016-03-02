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
use Sylius\Behat\Context\Setup\ProductContext;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @mixin ProductContext
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ProductContextSpec extends ObjectBehavior
{
    function let(
        SharedStorageInterface $sharedStorage,
        ProductRepositoryInterface $productRepository,
        FactoryInterface $productFactory,
        FactoryInterface $productVariantFactory,
        ObjectManager $objectManager
    ) {
        $this->beConstructedWith(
            $sharedStorage,
            $productRepository,
            $productFactory,
            $productVariantFactory,
            $objectManager
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\ProductContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_creates_new_product_variant(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $productVariantFactory,
        ObjectManager $objectManager,
        ProductInterface $product,
        ProductVariantInterface $productVariant
    ) {
        $productVariantFactory->createNew()->willReturn($productVariant);

        $productVariant->setPresentation('Han Solo Mug')->shouldBeCalled();
        $productVariant->setPrice(2500)->shouldBeCalled();
        $productVariant->setProduct($product)->shouldBeCalled();
        $product->addVariant($productVariant)->shouldBeCalled();

        $objectManager->flush()->shouldBeCalled();
        $sharedStorage->set('variant', $productVariant)->shouldBeCalled();

        $this->theProductHasVariantPricedAt($product, 'Han Solo Mug', '2500');
    }
}
