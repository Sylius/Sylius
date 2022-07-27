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

namespace Sylius\Component\Attribute\spec\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class AttributeDeletionCheckerSpec extends ObjectBehavior
{
    function let(RepositoryInterface $productAttributeRepository): void
    {
        $this->beConstructedWith($productAttributeRepository);
    }

    public function it_says_if_there_is_a_product_with_the_selected_attribute(
        RepositoryInterface $attributeValueRepository,
        ProductAttributeInterface $productAttribute,
        ProductInterface $product,
    ): void {
        $attributeValueRepository->findOneBy(['attribute' => $productAttribute])
            ->willReturn($product)
        ;

        $this->isDeletable($productAttribute)
            ->willReturn(false)
        ;
    }

    public function it_says_if_there_is_not_a_product_with_the_selected_attribute(
        RepositoryInterface $attributeValueRepository,
        ProductAttributeInterface $productAttribute,
        ProductInterface $product,
    ): void {
        $attributeValueRepository->findOneBy(['attribute' => $productAttribute])
            ->willReturn(null)
        ;

        $this->isDeletable($productAttribute)
            ->willReturn(true)
        ;
    }
}
