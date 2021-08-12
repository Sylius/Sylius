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

use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Webmozart\Assert\Assert;

/**
 * @experimental
 */
final class CommandDenormalizer implements ContextAwareDenormalizerInterface
{
    private const OBJECT_TO_POPULATE = 'object_to_populate';

    /** @var DenormalizerInterface */
    private $itemNormalizer;

    /** @var ClassMetadataFactoryInterface */
    private $classMetadataFactory;

    public function __construct(
        DenormalizerInterface $itemNormalizer,
        ClassMetadataFactoryInterface $classMetadataFactory
    )
    {
        $this->itemNormalizer = $itemNormalizer;
        $this->classMetadataFactory = $classMetadataFactory;
    }

    /** @psalm-suppress MissingParamType */
    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        /** @psalm-var class-string|null $inputClassName */
        $inputClassName = $context['input']['class'] ?? null;

        if ($inputClassName === null) {
            return $this->canBeConvertedFromIriToIdentifier($type);
        }

        return $this->canBeConvertedFromIriToIdentifier($inputClassName);
    }

    /** @psalm-suppress MissingParamType */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        if (isset($context[self::OBJECT_TO_POPULATE])) {
            return $this->itemNormalizer->denormalize($data, $type, $format, $context);
        }

        $constructor = (new \ReflectionClass($context['input']['class']))->getConstructor();
        Assert::notNull($constructor);

        $metadata = $this->classMetadataFactory->getMetadataFor($context['input']['class']);

        $missingFields = [];

        /** @psalm-suppress InternalMethod */
        foreach ($metadata->getAttributesMetadata() as $attributeMetadata) {
            $attributeMetadataName = $attributeMetadata->getSerializedName() !== null ? $attributeMetadata->getSerializedName() : $attributeMetadata->getName();

            foreach ($constructor->getParameters() as $constructorParameter) {
                if ($constructorParameter->getName() === $attributeMetadata->getName() && !isset($data[$attributeMetadataName]) && !($constructorParameter->allowsNull() || $constructorParameter->isDefaultValueAvailable())) {
                    $missingFields[] = $attributeMetadataName;
                }
            }
        }

        if (count($missingFields) > 0) {
            throw new MissingConstructorArgumentsException(
                sprintf('Request does not have the following required fields specified: %s.', implode(', ', $missingFields))
            );
        }

        return $this->itemNormalizer->denormalize($data, $type, $format, $context);
    }

    private function canBeConvertedFromIriToIdentifier(string $type): bool
    {
        return in_array(IriToIdentifierConversionAwareInterface::class, class_implements($type) ?? [], true);
    }
}
