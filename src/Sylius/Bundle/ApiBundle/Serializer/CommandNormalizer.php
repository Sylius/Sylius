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

use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @experimental
 */
final class CommandNormalizer implements ContextAwareNormalizerInterface
{
    private const ALREADY_CALLED = 'command_normalizer_already_called';

    /** @var NormalizerInterface */
    private $objectNormalizer;

    public function __construct(NormalizerInterface $objectNormalizer)
    {
        $this->objectNormalizer = $objectNormalizer;
    }

    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return method_exists($data, 'getClass') && $data->getClass() === MissingConstructorArgumentsException::class;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;
        $data = $this->objectNormalizer->normalize($object, $format, $context);

        return [
            'code' => 400,
            'message' => $data['message'],
        ];
    }
}
