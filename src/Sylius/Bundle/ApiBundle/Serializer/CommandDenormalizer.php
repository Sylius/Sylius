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
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @experimental
 */
final class CommandDenormalizer implements ContextAwareDenormalizerInterface
{
    /** @var DenormalizerInterface */
    private $itemNormalizer;

    public function __construct(DenormalizerInterface $itemNormalizer)
    {
        $this->itemNormalizer = $itemNormalizer;
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return isset($context['input']['class']);
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $parameters = (new \ReflectionClass($context['input']['class']))->getConstructor()->getParameters();

        $missingFields = [];
        foreach ($parameters as $parameter) {
            if (!isset($data[$parameter->getName()]) && !($parameter->allowsNull() || $parameter->isDefaultValueAvailable())) {
                $missingFields[] = $parameter->getName();
            }
        }

        if (count($missingFields) > 0) {
            throw new MissingConstructorArgumentsException(
                sprintf('Request does not have the following required fields specified: %s.', implode(', ', $missingFields))
            );
        }

        return $this->itemNormalizer->denormalize($data, $type, $format, $context);
    }
}
