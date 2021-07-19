<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Serializer;

use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CommandOperationNormalizer implements ContextAwareNormalizerInterface
{
    private const ALREADY_CALLED = 'command_operation_normalizer_already_called';

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
