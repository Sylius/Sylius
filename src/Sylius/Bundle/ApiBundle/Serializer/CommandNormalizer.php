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

use Sylius\Bundle\ApiBundle\Exception\InvalidRequestArgumentException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class CommandNormalizer implements ContextAwareNormalizerInterface
{
    private const ALREADY_CALLED = 'sylius_command_normalizer_already_called';

    public function __construct(private NormalizerInterface $objectNormalizer)
    {
    }

    public function supportsNormalization($data, $format = null, $context = []): bool
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
     * @param mixed $object
     * @param string|null $format
     * @param array<string, mixed> $context
     * @return array{ code: int, message: string }
     * @throws ExceptionInterface
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        $context[self::ALREADY_CALLED] = true;
        $data = $this->objectNormalizer->normalize($object, $format, $context);
        Assert::isArray($data);
        Assert::keyExists($data, 'message');

        return [
            'code' => 400,
            'message' => $data['message'],
        ];
    }
}
