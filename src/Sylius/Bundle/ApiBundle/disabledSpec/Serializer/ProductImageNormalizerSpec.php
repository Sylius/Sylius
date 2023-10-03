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

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProductImageNormalizerSpec extends ObjectBehavior
{
    function let(CacheManager $cacheManager, RequestStack $requestStack): void
    {
        $this->beConstructedWith(
            $cacheManager,
            $requestStack,
            'prefix',
            '/prefix',
            '/prefix/',
            'prefix/',
        );
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
        ProductImageInterface $productImage,
        RequestStack $requestStack,
        Request $request,
    ): void {
        $this->setNormalizer($normalizer);

        $normalizer->normalize($productImage, null, ['sylius_product_image_normalizer_already_called' => true])->willReturn(['path' => 'some_path']);

        $requestStack->getCurrentRequest()->willReturn($request);
        $request->query = new ParameterBag([]);

        $this->normalize($productImage, null, [])->shouldReturn(['path' => '/prefix/some_path']);
    }

    function it_serializes_filtered_path_to_product_image(
        NormalizerInterface $normalizer,
        ProductImageInterface $productImage,
        RequestStack $requestStack,
        Request $request,
        CacheManager $cacheManager,
    ): void {
        $this->setNormalizer($normalizer);

        $normalizer->normalize($productImage, null, ['sylius_product_image_normalizer_already_called' => true])->willReturn(['path' => 'some_path']);

        $requestStack->getCurrentRequest()->willReturn($request);
        $request->query = new ParameterBag(['filter' => 'sylius_large']);

        $cacheManager->getBrowserPath('some_path', 'sylius_large')->willReturn('/sylius_large/some_path');

        $this->normalize($productImage, null, [])->shouldReturn(['path' => '/sylius_large/some_path']);
    }
}
