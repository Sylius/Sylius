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

use Sylius\Component\Attribute\AttributeType\DateAttributeType;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Webmozart\Assert\Assert;

/** @experimental */
final class ProductAttributeValueNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'sylius_product_attribute_value_normalizer_already_called';

    public function __construct(
        private LocaleProviderInterface $localeProvider,
        private string $defaultLocaleCode,
    ) {
    }

    /**
     * @param ProductAttributeValueInterface $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, ProductAttributeValueInterface::class);
        Assert::keyNotExists($context, self::ALREADY_CALLED);

        $context[self::ALREADY_CALLED] = true;
        $data = $this->normalizer->normalize($object, $format, $context);

        $data['value'] = match ($object->getType()) {
            SelectAttributeType::TYPE => $this->normalizeSelectValue($object, $context),
            DateAttributeType::TYPE => $object->getValue()->format('Y-m-d'),
            default => $data['value'],
        };

        return $data;
    }

    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof ProductAttributeValueInterface;
    }

    private function normalizeSelectValue(ProductAttributeValueInterface $object, array $context): array
    {
        $attribute = $object->getAttribute();
        $configuration = $attribute->getConfiguration();

        $values = [];

        foreach ($configuration['choices'] ?? [] as $uuid => $choice) {
            if (in_array($uuid, $object->getValue())) {
                $values[] = $choice[$object->getLocaleCode()]
                    ?? $choice[$this->localeProvider->getDefaultLocaleCode()]
                    ?? $choice[$this->defaultLocaleCode]
                    ?? reset($choice);
            }
        }

        return $values;
    }
}
