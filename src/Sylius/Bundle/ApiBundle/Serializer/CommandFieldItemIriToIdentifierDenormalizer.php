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
use Sylius\Bundle\ApiBundle\Command\CommandFieldItemIriToIdentifierAwareInterface;
use Sylius\Bundle\ApiBundle\Converter\ItemIriToIdentifierConverterInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/** @experimental */
final class CommandFieldItemIriToIdentifierDenormalizer implements ContextAwareDenormalizerInterface
{
    /** @var DenormalizerInterface */
    private $objectNormalizer;

    /** @var ItemIriToIdentifierConverterInterface */
    private $itemIriToIdentifierConverter;

    /** @var DataTransformerInterface */
    private $commandAwareInputDataTransformer;

    public function __construct(
        DenormalizerInterface $objectNormalizer,
        ItemIriToIdentifierConverterInterface $itemIriToIdentifierConverter,
        DataTransformerInterface $commandAwareInputDataTransformer
    ) {
        $this->objectNormalizer = $objectNormalizer;
        $this->itemIriToIdentifierConverter = $itemIriToIdentifierConverter;
        $this->commandAwareInputDataTransformer = $commandAwareInputDataTransformer;
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = [])
    {
        /** @psalm-var class-string $inputClassName|null */
        $inputClassName = $this->getInputClassName($context);

        if ($inputClassName === null) {
            return false;
        }

        foreach (class_implements($inputClassName) as $classInterface) {
            if ($classInterface === CommandFieldItemIriToIdentifierAwareInterface::class) {
                return true;
            }
        }

        return false;
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        /** @psalm-var class-string $inputClassName */
        $inputClassName = $this->getInputClassName($context);

        foreach (class_implements($inputClassName) as $classInterface) {
            if ($classInterface !== CommandFieldItemIriToIdentifierAwareInterface::class) {
                continue;
            }

            foreach ($data as $classFieldName => $classFieldValue) {
                if ($this->itemIriToIdentifierConverter->isIdentifier($data[$classFieldName]) && $data[$classFieldName] != '') {
                    $data[$classFieldName] = $this->itemIriToIdentifierConverter->getIdentifier((string) $data[$classFieldName]);
                }
            }
        }

        $denormalizedInput = $this->objectNormalizer->denormalize($data, $this->getInputClassName($context), $format, $context);

        if ($this->commandAwareInputDataTransformer->supportsTransformation($denormalizedInput, $type, $context)) {
            return $this->commandAwareInputDataTransformer->transform($denormalizedInput, $type, $context);
        }

        return $denormalizedInput;
    }

    private function getInputClassName(array $context): ?string
    {
        return $context['input']['class'] ?? null;
    }
}
