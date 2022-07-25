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

use Sylius\Bundle\ApiBundle\Provider\ProductImageFilterProviderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/** @experimental */
final class ProductImageDocumentationNormalizer implements NormalizerInterface
{
    public function __construct(
        private NormalizerInterface $decoratedNormalizer,
        private ProductImageFilterProviderInterface $filterProvider,
        private string $apiRoute,
    ) {
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->decoratedNormalizer->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $docs = $this->decoratedNormalizer->normalize($object, $format, $context);

        $enums = $this->filterProvider->provideShopFilters();
        $enums = array_keys($enums);

        $shopProductImagePath = $this->apiRoute . '/shop/product-images/{id}';
        $params = $docs['paths'][$shopProductImagePath]['get']['parameters'];

        foreach ($params as &$param) {
            if ($param['in'] === 'query') {
                if (is_string($param['schema']['enum']) || $param['schema']['enum'] === null) {
                    $param['schema']['enum'] = $enums;

                    break;
                }

                $param['schema']['enum'] = array_merge($param['schema']['enum'], $enums);
            }
        }

        return $docs;
    }
}
