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

use ApiPlatform\Api\IriConverterInterface;
use Sylius\Bundle\ApiBundle\Exception\InvalidProductAttributeValueTypeException;
use Sylius\Component\Attribute\AttributeType\DateAttributeType;
use Sylius\Component\Attribute\AttributeType\DatetimeAttributeType;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Webmozart\Assert\Assert;

/** @experimental */
final class ProductDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'sylius_product_denormalizer_already_called';

    public function __construct(
        private IriConverterInterface $iriConverter,
    ) {
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return
            !isset($context[self::ALREADY_CALLED]) &&
            is_array($data) &&
            is_a($type, ProductInterface::class, true)
        ;
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;
        $data = (array) $data;

        $data = $this->denormalizeOptions($data, $context);

        $this->validateAttributes($data);
        $data = $this->denormalizeAttributes($data);

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    /**
     * @param array<array-key, mixed> $data
     * @param array<array-key, mixed> $context
     *
     * @return array<array-key, mixed>
     */
    private function denormalizeOptions(array $data, array $context): array
    {
        if (!isset($context[AbstractNormalizer::OBJECT_TO_POPULATE])) {
            return $data;
        }

        if (!isset($data['options'])) {
            return $data;
        }

        /** @var ProductInterface $product */
        $product = $context[AbstractNormalizer::OBJECT_TO_POPULATE];
        Assert::isInstanceOf($product, ProductInterface::class);

        if (!$product->getVariants()->isEmpty()) {
            unset($data['options']);
        }

        return $data;
    }

    /**
     * @param array<array-key, mixed> $data
     *
     * @return array<array-key, mixed>
     */
    private function denormalizeAttributes(array $data): array
    {
        if (!isset($data['attributes'])) {
            return $data;
        }

        foreach ($data['attributes'] as $key => $attributeData) {
            /** @var ProductAttributeInterface $attribute */
            $attribute = $this->iriConverter->getResourceFromIri($attributeData['attribute']);

            if (in_array($attribute->getType(), [DateAttributeType::TYPE, DateTimeAttributeType::TYPE], true)) {
                $attributeData['value'] = new \DateTime($attributeData['value']);
                $data['attributes'][$key] = $attributeData;
            }
        }

        return $data;
    }

    /** @param array<array-key, mixed> $data */
    private function validateAttributes(array $data): void
    {
        if (!isset($data['attributes'])) {
            return;
        }

        foreach ($data['attributes'] as ['attribute' => $attributeIri, 'value' => $value]) {
            if ($value === null) {
                continue;
            }

            /** @var ProductAttributeInterface $attribute */
            $attribute = $this->iriConverter->getResourceFromIri($attributeIri);

            switch ($attribute->getStorageType()) {
                case AttributeValueInterface::STORAGE_BOOLEAN:
                    if (!is_bool($value)) {
                        $this->throwException($attribute->getName(), $attribute->getStorageType());
                    }

                    return;
                case AttributeValueInterface::STORAGE_INTEGER:
                    if (!is_int($value)) {
                        $this->throwException($attribute->getName(), $attribute->getStorageType());
                    }

                    return;
                case AttributeValueInterface::STORAGE_FLOAT:
                    if (!is_int($value) && !is_float($value)) {
                        $this->throwException($attribute->getName(), $attribute->getStorageType());
                    }

                    return;
                case AttributeValueInterface::STORAGE_JSON:
                    if (!is_array($value)) {
                        $this->throwException($attribute->getName(), 'array');
                    }

                    return;
                default:
                    if (!is_string($value)) {
                        $this->throwException($attribute->getName(), 'string');
                    }
            }
        }
    }

    private function throwException(string $attributeName, string $type): void
    {
        throw new InvalidProductAttributeValueTypeException(sprintf(
            'The value of attribute "%s" has an invalid type, it must be of type %s.',
            $attributeName,
            $type,
        ));
    }
}
