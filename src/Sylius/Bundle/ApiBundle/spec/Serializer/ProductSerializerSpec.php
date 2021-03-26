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

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Order\Model\Order;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProductSerializerSpec extends ObjectBehavior
{
    function let(
        NormalizerInterface $objectNormalizer
    ): void {
        $this->beConstructedWith($objectNormalizer, 3);
    }

    function it_supports_only_product_interface(): void {
        $product = new Product();
        $this->supportsNormalization($product)->shouldReturn(true);

        $order = new Order();
        $this->supportsNormalization($order)->shouldReturn(false);
    }

    function it_does_not_serialize_if_item_operation_name_is_not_shop_get(): void {
        $product = new Product();
        $this->supportsNormalization($product, null, ['item_operation_name' => 'shop_admin'])->shouldReturn(false);
    }

    function it_serializes_product_if_item_operation_name_is_shop_get(
        NormalizerInterface $objectNormalizer
    ): void {
        $product = new Product();
        $product->getId()->willReturn(20);

        $objectNormalizer->normalize($product, null, [])->willReturn([]);

        $this->normalize($product, null, [])->shouldReturn(['@id' => 20]);
    }
}
