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

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;
use Sylius\Bundle\ApiBundle\Converter\IriToIdentifierConverterInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/** @experimental */
final class CommandArgumentsDenormalizer implements ContextAwareDenormalizerInterface
{
    /** @var DenormalizerInterface */
    private $objectNormalizer;

    /** @var IriToIdentifierConverterInterface */
    private $iriToIdentifierConverter;

    /** @var DataTransformerInterface */
    private $commandAwareInputDataTransformer;

    public function __construct(
        DenormalizerInterface $objectNormalizer,
        IriToIdentifierConverterInterface $iriToIdentifierConverter,
        DataTransformerInterface $commandAwareInputDataTransformer
    ) {
        $this->objectNormalizer = $objectNormalizer;
        $this->iriToIdentifierConverter = $iriToIdentifierConverter;
        $this->commandAwareInputDataTransformer = $commandAwareInputDataTransformer;
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = [])
    {
        /** @psalm-var class-string $inputClassName|null */
        $inputClassName = $this->getInputClassName($context);

        if ($inputClassName === null) {
            return $this->canBeConvertedFromIriToIdentifier($type);
        }

        return $this->canBeConvertedFromIriToIdentifier($inputClassName);
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        /** @psalm-var class-string|null $inputClassName */
        $targetClassName = $this->getInputClassName($context);

        if ($targetClassName === null) {
            $targetClassName = $type;
        }

        foreach (class_implements($targetClassName) as $classInterface) {
            if ($classInterface !== IriToIdentifierConversionAwareInterface::class) {
                continue;
            }

            foreach ($data as $classFieldName => $classFieldValue) {
                if ($this->iriToIdentifierConverter->isIdentifier($data[$classFieldName]) && $data[$classFieldName] != '') {
                    $data[$classFieldName] = $this->iriToIdentifierConverter->getIdentifier((string) $data[$classFieldName]);
                }
            }
        }

        $denormalizedInput = $this->objectNormalizer->denormalize($data, $targetClassName, $format, $context);

        if ($this->commandAwareInputDataTransformer->supportsTransformation($denormalizedInput, $targetClassName, $context)) {
            return $this->commandAwareInputDataTransformer->transform($denormalizedInput, $targetClassName, $context);
        }

        return $denormalizedInput;
    }

    private function getInputClassName(array $context): ?string
    {
        return $context['input']['class'] ?? null;
    }

    private function canBeConvertedFromIriToIdentifier(string $type): bool
    {
        return in_array(IriToIdentifierConversionAwareInterface::class, class_implements($type) ?? [], true);
    }
}
