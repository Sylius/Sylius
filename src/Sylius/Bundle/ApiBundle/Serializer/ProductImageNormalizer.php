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

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Core\Model\ProductImageInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Webmozart\Assert\Assert;

class ProductImageNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'sylius_product_image_normalizer_already_called';

    private string $prefix;

    public function __construct(
        private CacheManager $cacheManager,
        private RequestStack $requestStack,
        string $prefix,
    ) {
        $this->prefix = $this->validatePrefix($prefix);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, ProductImageInterface::class);
        Assert::keyNotExists($context, self::ALREADY_CALLED);

        $context[self::ALREADY_CALLED] = true;

        $data = $this->normalizer->normalize($object, $format, $context);

        return $this->resolvePath($data);
    }

    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof ProductImageInterface;
    }

    private function validatePrefix(string $prefix): string
    {
        if (\DIRECTORY_SEPARATOR !== substr($prefix, 0, 1)) {
            $prefix = \DIRECTORY_SEPARATOR . $prefix;
        }

        if (\DIRECTORY_SEPARATOR === substr($prefix, -1)) {
            return $prefix;
        }

        return $prefix . \DIRECTORY_SEPARATOR;
    }

    private function resolvePath($data): array
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request->query->has('filter')) {
            /** @var string $filter */
            $filter = $request->query->get('filter');

            $data['path'] = $this->cacheManager->getBrowserPath(parse_url($data['path'], \PHP_URL_PATH), $filter);

            return $data;
        }

        $data['path'] = $this->prefix . $data['path'];

        return $data;
    }
}
