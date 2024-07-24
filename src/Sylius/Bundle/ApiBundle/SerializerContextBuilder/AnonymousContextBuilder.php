<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\SerializerContextBuilder;

use ApiPlatform\JsonLd\AnonymousContextBuilderInterface;
use ApiPlatform\JsonLd\ContextBuilder;
use ApiPlatform\Metadata\UrlGeneratorInterface;
use Sylius\Component\Core\Customer\Statistics\PerChannelCustomerStatistics;

final class AnonymousContextBuilder implements AnonymousContextBuilderInterface
{
    public function __construct(private readonly AnonymousContextBuilderInterface $baseContextBuilder)
    {
    }

    public function getAnonymousResourceContext($object, array $context = [], int $referenceType = UrlGeneratorInterface::ABS_PATH): array
    {
        if ($object instanceof PerChannelCustomerStatistics) {
            $context['gen_id'] = false;
        }

        return $this->baseContextBuilder->getAnonymousResourceContext($object, $context, $referenceType);
    }

    public function getBaseContext(int $referenceType = UrlGeneratorInterface::ABS_PATH): array
    {
        $this->baseContextBuilder->getBaseContext($referenceType);
    }

    public function getEntrypointContext(int $referenceType = UrlGeneratorInterface::ABS_PATH): array
    {
        return $this->baseContextBuilder->getEntrypointContext($referenceType);
    }

    public function getResourceContext(string $resourceClass, int $referenceType = UrlGeneratorInterface::ABS_PATH): array
    {
        return $this->baseContextBuilder->getResourceContext($resourceClass, $referenceType);
    }

    public function getResourceContextUri(string $resourceClass, int $referenceType = UrlGeneratorInterface::ABS_PATH): string
    {
        return $this->baseContextBuilder->getResourceContextUri($resourceClass, $referenceType);
    }
}
