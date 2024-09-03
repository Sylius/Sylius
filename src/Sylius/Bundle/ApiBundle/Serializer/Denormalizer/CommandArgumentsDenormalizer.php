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

namespace Sylius\Bundle\ApiBundle\Serializer\Denormalizer;

use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;
use Sylius\Bundle\ApiBundle\Converter\IriToIdentifierConverterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CommandArgumentsDenormalizer implements DenormalizerInterface
{
    public function __construct(
        private DenormalizerInterface $commandDenormalizer,
        private IriToIdentifierConverterInterface $iriToIdentifierConverter,
    ) {
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        /** @var class-string|null $inputClassName */
        $inputClassName = $this->getInputClassName($context);

        if ($inputClassName === null) {
            return false;
        }

        return is_subclass_of($inputClassName, IriToIdentifierConversionAwareInterface::class);
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        /** @var class-string $inputClassName */
        $inputClassName = $this->getInputClassName($context);

        if (is_subclass_of($inputClassName, IriToIdentifierConversionAwareInterface::class)) {
            $data = $this->convertIrisToIdentifiers($data);
        }

        return $this->commandDenormalizer->denormalize($data, $inputClassName, $format, $context);
    }

    private function getInputClassName(array $context): ?string
    {
        return $context['input']['class'] ?? null;
    }

    /**
     * @param array<array-key, mixed>|string|int|mixed $data
     *
     * @return array<array-key, mixed>|string|int|mixed
     */
    private function convertIrisToIdentifiers(mixed $data): mixed
    {
        if (is_string($data) && $data !== '' && $this->iriToIdentifierConverter->isIdentifier($data)) {
            return $this->iriToIdentifierConverter->getIdentifier($data);
        }

        if (is_array($data)) {
            foreach ($data as $classFieldName => $classFieldValue) {
                $data[$classFieldName] = $this->convertIrisToIdentifiers($classFieldValue);
            }
        }

        return $data;
    }
}
