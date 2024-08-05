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

namespace Sylius\Bundle\ApiBundle\Serializer\Normalizer;

use Sylius\Bundle\ApiBundle\Exception\InvalidRequestArgumentException;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class CommandNormalizer implements NormalizerInterface
{
    private const ALREADY_CALLED = 'sylius_command_normalizer_already_called';

    public function __construct(private NormalizerInterface $objectNormalizer)
    {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return
            is_object($data) &&
            method_exists($data, 'getClass') &&
            ($data->getClass() === MissingConstructorArgumentsException::class || $data->getClass() === InvalidRequestArgumentException::class)
        ;
    }

    /**
     * @return array{code: int, message: string}
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $context[self::ALREADY_CALLED] = true;
        $data = $this->objectNormalizer->normalize($object, $format, $context);

        return [
            'code' => 400,
            'message' => $data['message'],
        ];
    }
}
