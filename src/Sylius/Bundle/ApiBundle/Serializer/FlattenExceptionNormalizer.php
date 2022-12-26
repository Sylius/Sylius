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

namespace Sylius\Bundle\ApiBundle\Serializer;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

final class FlattenExceptionNormalizer implements ContextAwareNormalizerInterface
{
    public function __construct(
        private ContextAwareNormalizerInterface $decorated,
        private RequestStack $requestStack,
        private string $newApiRoute,
    ) {
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        if (method_exists($this->requestStack, 'getMainRequest')) {
            $path = $this->requestStack->getMainRequest()->getPathInfo();
        } else {
            /** @phpstan-ignore-next-line */
            $path = $this->requestStack->getMasterRequest()->getPathInfo();
        }

        if (str_starts_with($path, $this->newApiRoute)) {
            return false;
        }

        return $this->decorated->supportsNormalization($data, $format, $context);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        return $this->decorated->normalize($object, $format, $context);
    }
}
