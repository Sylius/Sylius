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

namespace Sylius\Bundle\ApiBundle\Serializer;

use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Webmozart\Assert\Assert;

final class ProductDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'sylius_product_denormalizer_already_called';

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return
            !isset($context[self::ALREADY_CALLED]) &&
            is_array($data) &&
            is_a($type, ProductInterface::class, true)
        ;
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;
        $data = (array) $data;

        $data = $this->denormalizeOptions($data, $context);

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    /**
     * @param array<array-key, mixed> $data
     * @param array<array-key, mixed> $context
     *
     * @return array<array-key, mixed>
     */
    private function denormalizeOptions(array $data, array $context): array
    {
        if (!isset($context[AbstractNormalizer::OBJECT_TO_POPULATE])) {
            return $data;
        }

        if (!isset($data['options'])) {
            return $data;
        }

        /** @var ProductInterface $product */
        $product = $context[AbstractNormalizer::OBJECT_TO_POPULATE];
        Assert::isInstanceOf($product, ProductInterface::class);

        if (!$product->getVariants()->isEmpty()) {
            unset($data['options']);
        }

        return $data;
    }
}
