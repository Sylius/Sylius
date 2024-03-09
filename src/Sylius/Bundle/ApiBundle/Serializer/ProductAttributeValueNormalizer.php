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

use Sylius\Component\Attribute\AttributeType\DateAttributeType;
use Sylius\Component\Attribute\AttributeType\DatetimeAttributeType;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Webmozart\Assert\Assert;

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

        switch ($object->getType()) {
            case SelectAttributeType::TYPE:
                $data['value'] = $this->normalizeSelectValue($object);

                break;
            case DateAttributeType::TYPE:
                Assert::isInstanceOf($object->getValue(), \DateTimeInterface::class);
                $data['value'] = $object->getValue()->format('Y-m-d');

                break;
            case DateTimeAttributeType::TYPE:
                Assert::isInstanceOf($object->getValue(), \DateTimeInterface::class);
                $data['value'] = $object->getValue()->format('Y-m-d H:i:s');

                break;
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof ProductAttributeValueInterface;
    }

    private function normalizeSelectValue(ProductAttributeValueInterface $object): array
    {
        $value = $object->getValue();
        if (!is_array($value)) {
            return [];
        }

        $attribute = $object->getAttribute();
        $configuration = $attribute->getConfiguration();
        $defaultLocaleCode = $this->localeProvider->getDefaultLocaleCode();

        $values = [];

        foreach ($configuration['choices'] ?? [] as $uuid => $choice) {
            if (in_array($uuid, $value)) {
                $values[] = $choice[$object->getLocaleCode()]
                    ?? $choice[$defaultLocaleCode]
                    ?? $choice[$this->defaultLocaleCode]
                    ?? reset($choice)
                ;
            }
        }

        return $values;
    }
}
