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

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use Sylius\Bundle\ApiBundle\Attribute\ChannelCodeAware;
use Sylius\Bundle\ApiBundle\Command\ChannelCodeAwareInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final readonly class ChannelAwareContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct (
        private SerializerContextBuilderInterface $decorated,
        private ChannelContextInterface $channelContext,
    ) {
    }

    /**
     * @param array<string>|null $extractedAttributes
     * @return array<string, mixed>
     */
    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $inputClass = $this->getInputClassFromContext($context);

        if ($inputClass === null || !$this->isChannelAware($inputClass)) {
            return $context;
        }

        $constructorArgumentName = $this->getConstructorArgumentName($inputClass) ?? 'channelCode';

        $context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS][$inputClass] = [
            $constructorArgumentName => $this->channelContext->getChannel()->getCode(),
        ];

        return $context;
    }

    /**
     * @param array<string, mixed> $context
     */
    private function getInputClassFromContext(array $context): ?string
    {
        return $context['input']['class'] ?? null;
    }

    private function isChannelAware(string $inputClass): bool
    {
        return is_a($inputClass, ChannelCodeAwareInterface::class, true);
    }

    private function getConstructorArgumentName(string $class): ?string
    {
        $classReflection = new \ReflectionClass($class);
        $attributes = $classReflection->getAttributes(ChannelCodeAware::class);

        if (count($attributes) === 0) {
            return null;
        }

        /** @var ChannelCodeAware $channelCodeAwareAttribute */
        $channelCodeAwareAttribute = $attributes[0]->newInstance();

        return $channelCodeAwareAttribute->constructorArgumentName;
    }
}
