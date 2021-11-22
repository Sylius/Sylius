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

namespace Sylius\Bundle\ApiBundle\Swagger;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/** @experimental */
final class ProductSlugDocumentationNormalizer implements NormalizerInterface
{
    private const PRODUCT_SLUG_PATH = '/api/v2/shop/products-by-slug/{slug}';

    private NormalizerInterface $decoratedNormalizer;

    public function __construct(NormalizerInterface $decoratedNormalizer)
    {
        $this->decoratedNormalizer = $decoratedNormalizer;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $this->decoratedNormalizer->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $docs = $this->decoratedNormalizer->normalize($object, $format, $context);

        $params = $docs['paths'][self::PRODUCT_SLUG_PATH]['get']['parameters'];

        foreach ($params as $index => $param) {
            if ($param['name'] === 'code') {
                unset($docs['paths'][self::PRODUCT_SLUG_PATH]['get']['parameters'][$index]);
            }
        }

        return $docs;
    }
}
