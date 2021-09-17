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

use Sylius\Bundle\ApiBundle\Converter\ItemIriToIdentifierConverterInterface;
use Sylius\Bundle\ApiBundle\DataTransformer\CommandAwareInputDataTransformer;
use Sylius\Bundle\ApiBundle\Map\CommandItemIriArgumentToIdentifierMapInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CommandFieldItemIriToIdentifierDenormalizer implements ContextAwareDenormalizerInterface
{
    private DenormalizerInterface $objectNormalizer;

    private ItemIriToIdentifierConverterInterface $itemIriToIdentifierConverter;

    private CommandAwareInputDataTransformer $commandAwareInputDataTransformer;

    private CommandItemIriArgumentToIdentifierMapInterface $commandItemIriArgumentToIdentifierMap;

    public function __construct(
        DenormalizerInterface $objectNormalizer,
        ItemIriToIdentifierConverterInterface $itemIriToIdentifierConverter,
        CommandAwareInputDataTransformer $commandAwareInputDataTransformer,
        CommandItemIriArgumentToIdentifierMapInterface $commandItemIriArgumentToIdentifierMap
    ) {
        $this->objectNormalizer = $objectNormalizer;
        $this->itemIriToIdentifierConverter = $itemIriToIdentifierConverter;
        $this->commandAwareInputDataTransformer = $commandAwareInputDataTransformer;
        $this->commandItemIriArgumentToIdentifierMap = $commandItemIriArgumentToIdentifierMap;
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = [])
    {
        return $this->commandItemIriArgumentToIdentifierMap->has($this->getInputClassName($context));
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        /** @psalm-var class-string $inputClassName */
        $inputClassName = $this->getInputClassName($context);

        $fieldName = $this->commandItemIriArgumentToIdentifierMap->get($inputClassName);

        if (array_key_exists($fieldName, $data)) {
            $data[$fieldName] = $this->itemIriToIdentifierConverter->getIdentifier($data[$fieldName]);
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
