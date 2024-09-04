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

use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Resource\Translation\TranslatableEntityLocaleAssignerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class ProductOptionValueNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'sylius_product_option_value_normalizer_already_called';

    public function __construct(private readonly TranslatableEntityLocaleAssignerInterface $translatableEntityLocaleAssigner)
    {
    }

    /**
     * @param ProductOptionValueInterface $object
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        Assert::isInstanceOf($object, ProductOptionValueInterface::class);
        Assert::keyNotExists($context, self::ALREADY_CALLED);

        $context[self::ALREADY_CALLED] = true;

        $this->translatableEntityLocaleAssigner->assignLocale($object);

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof ProductOptionValueInterface;
    }
}
