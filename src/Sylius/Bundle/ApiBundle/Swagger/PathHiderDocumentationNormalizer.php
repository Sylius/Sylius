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

class PathHiderDocumentationNormalizer implements NormalizerInterface
{
    private NormalizerInterface $decoratedNormalizer;

    private array $apiRoutes;

    public function __construct(NormalizerInterface $decoratedNormalizer, array $apiRoutes)
    {
        $this->decoratedNormalizer = $decoratedNormalizer;
        $this->apiRoutes = $apiRoutes;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $this->decoratedNormalizer->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $docs = $this->decoratedNormalizer->normalize($object, $format, $context);
        $paths = (array) $docs['paths'];

        foreach ($this->apiRoutes as $apiRoute) {
            if (array_key_exists($apiRoute, $paths)) {
                unset($paths[$apiRoute]);
            }
        }

        $docs['paths'] = $paths;

        return $docs;
    }
}
