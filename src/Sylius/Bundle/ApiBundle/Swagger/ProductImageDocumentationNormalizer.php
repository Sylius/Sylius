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
class ProductImageDocumentationNormalizer implements NormalizerInterface
{
    private const SHOP_ITEM_PATH = '/api/v2/shop/product-images/{id}';

    public function __construct(
        private NormalizerInterface $decoratedNormalizer,
        private ProductImageFilterProviderInterface $filterProvider
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

        $params = $docs['paths'][self::SHOP_ITEM_PATH]['get']['parameters'];

        foreach ($params as $index => $param) {
            if ($param['in'] === 'query') {
                if (is_string($param['schema']['enum']) || $param['schema']['enum'] === null) {
                    $docs['paths'][self::SHOP_ITEM_PATH]['get']['parameters'][$index]['schema']['enum'] = $enums;

                    break;
                }

                $docs['paths'][self::SHOP_ITEM_PATH]['get']['parameters'][$index]['schema']['enum'] = array_merge($param['schema']['enum'], $enums);
            }
        }

        return $docs;
    }
}
