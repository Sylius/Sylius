<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ApiBundle\Serializer;

use Sylius\Component\Core\Model\ProductImageInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Webmozart\Assert\Assert;

/** @experimental */
class ProductImageNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'product_image_normalizer_already_called';

    /** @var string */
    private $prefix;

    public function __construct(string $prefix)
    {
        $this->prefix = $this->validatePrefix($prefix);
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, ProductImageInterface::class);
        Assert::keyNotExists($context, self::ALREADY_CALLED);

        $context[self::ALREADY_CALLED] = true;

        $data = $this->normalizer->normalize($object, $format, $context);

        $data['media_path'] = $this->prefix . $data['path'];

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof ProductImageInterface;
    }

    private function validatePrefix(string $prefix): string
    {
        if ('/' !== substr($prefix, 0)) {
            $prefix = '/'.$prefix;
        }

        if ('/' === substr($prefix, -1)) {
            return $prefix;
        }

        return $prefix . '/';
    }
}
