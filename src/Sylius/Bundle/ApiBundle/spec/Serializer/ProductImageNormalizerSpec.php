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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProductImageNormalizerSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('prefix', '/prefix', '/prefix/', 'prefix/');
    }

    function it_implements_context_aware_normalizer_interface(): void
    {
        $this->shouldImplement(ContextAwareNormalizerInterface::class);
    }

    function it_supports_only_product_image_interface(ProductImageInterface $productImage, OrderInterface $order): void
    {
        $this->supportsNormalization($productImage)->shouldReturn(true);
        $this->supportsNormalization($order)->shouldReturn(false);
    }

    function it_serializes_product_image_with_proper_prefix(
        NormalizerInterface $normalizer,
        ProductImageInterface $productImage
    ): void {
        $this->setNormalizer($normalizer);

        $normalizer->normalize($productImage, null, ['product_image_normalizer_already_called' => true])->willReturn(['path' => 'some_path']);

        $this->normalize($productImage, null, [])->shouldReturn(['path' => '/prefix/some_path']);
    }
}
