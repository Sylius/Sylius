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

namespace Sylius\Bundle\ApiBundle\ApiPlatform\JsonLd\Serializer;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Api\ResourceClassResolverInterface;
use ApiPlatform\Api\UrlGeneratorInterface;
use ApiPlatform\JsonLd\AnonymousContextBuilderInterface;
use ApiPlatform\JsonLd\ContextBuilderInterface;
use ApiPlatform\JsonLd\Serializer\JsonLdContextTrait;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Util\ClassInfoTrait;
use ApiPlatform\Serializer\AbstractItemNormalizer;
use ApiPlatform\Serializer\ContextTrait;
use ApiPlatform\Symfony\Security\ResourceAccessCheckerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Overridden class from the API Platform in order to pass the context to the IRI conversion
 *
 * Converts between objects and array including JSON-LD and Hydra metadata.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class ItemNormalizer extends AbstractItemNormalizer
{
    use ClassInfoTrait;
    use ContextTrait;
    use JsonLdContextTrait;

    public const FORMAT = 'jsonld';

    public function __construct(
        ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
        PropertyNameCollectionFactoryInterface $propertyNameCollectionFactory,
        PropertyMetadataFactoryInterface $propertyMetadataFactory,
        IriConverterInterface $iriConverter,
        ResourceClassResolverInterface $resourceClassResolver,
        private readonly ContextBuilderInterface $contextBuilder,
        PropertyAccessorInterface $propertyAccessor = null,
        NameConverterInterface $nameConverter = null,
        ClassMetadataFactoryInterface $classMetadataFactory = null,
        array $defaultContext = [],
        ResourceAccessCheckerInterface $resourceAccessChecker = null
    ) {
        parent::__construct(
            $propertyNameCollectionFactory,
            $propertyMetadataFactory,
            $iriConverter,
            $resourceClassResolver,
            $propertyAccessor,
            $nameConverter,
            $classMetadataFactory,
            $defaultContext,
            $resourceMetadataCollectionFactory,
            $resourceAccessChecker
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return self::FORMAT === $format && parent::supportsNormalization($data, $format, $context);
    }

    public function getSupportedTypes($format): array
    {
        return self::FORMAT === $format ? parent::getSupportedTypes($format) : [];
    }

    /**
     * {@inheritdoc}
     *
     * @throws LogicException
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $resourceClass = $this->getObjectClass($object);

        if ($this->getOutputClass($context)) {
            return parent::normalize($object, $format, $context);
        }

        // TODO: we should not remove the resource_class in the normalizeRawCollection as we would find out anyway that it's not the same as the requested one
        $previousResourceClass = $context['resource_class'] ?? null;
        $metadata = [];
        if ($isResourceClass = $this->resourceClassResolver->isResourceClass($resourceClass)) {
            $resourceClass = $this->resourceClassResolver->getResourceClass($object, $context['resource_class'] ?? null);
            $context = $this->initContext($resourceClass, $context);
            $metadata = $this->addJsonLdContext($this->contextBuilder, $resourceClass, $context);
        } elseif ($this->contextBuilder instanceof AnonymousContextBuilderInterface) {
            if ($context['api_collection_sub_level'] ?? false) {
                unset($context['api_collection_sub_level']);
                $context['output']['genid'] = true;
                $context['output']['iri'] = null;
            }

            // We should improve what's behind the context creation, its probably more complicated then it should
            $metadata = $this->createJsonLdContext($this->contextBuilder, $object, $context);
        }

        // maybe not needed anymore
        if (isset($context['operation']) && $previousResourceClass !== $resourceClass) {
            unset($context['operation'], $context['operation_name']);
        }

        if ($iri = $this->iriConverter->getIriFromResource($object, UrlGeneratorInterface::ABS_PATH, $context['operation'] ?? null, $context)) {
            $context['iri'] = $iri;
            $metadata['@id'] = $iri;
        }

        $context['api_normalize'] = true;

        $data = parent::normalize($object, $format, $context);
        if (!\is_array($data)) {
            return $data;
        }

        if (!isset($metadata['@type']) && $isResourceClass) {
            $operation = $context['operation'] ?? $this->resourceMetadataCollectionFactory->create($resourceClass)->getOperation();

            $types = $operation instanceof HttpOperation ? $operation->getTypes() : null;
            if (null === $types) {
                $types = [$operation->getShortName()];
            }
            $metadata['@type'] = 1 === \count($types) ? $types[0] : $types;
        }

        return $metadata + $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return self::FORMAT === $format && parent::supportsDenormalization($data, $type, $format, $context);
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotNormalizableValueException
     */
    public function denormalize(mixed $data, string $class, string $format = null, array $context = []): mixed
    {
        // Avoid issues with proxies if we populated the object
        if (isset($data['@id']) && !isset($context[self::OBJECT_TO_POPULATE])) {
            if (true !== ($context['api_allow_update'] ?? true)) {
                throw new NotNormalizableValueException('Update is not allowed for this operation.');
            }

            $context[self::OBJECT_TO_POPULATE] = $this->iriConverter->getResourceFromIri($data['@id'], $context + ['fetch_data' => true]);
        }

        return parent::denormalize($data, $class, $format, $context);
    }

    protected function normalizeRelation(
        ApiProperty $propertyMetadata,
        ?object $relatedObject,
        string $resourceClass,
        ?string $format,
        array $context
    ): \ArrayObject|array|string|null {
        if (null === $relatedObject || !empty($context['attributes']) || $propertyMetadata->isReadableLink()) {
            if (!$this->serializer instanceof NormalizerInterface) {
                throw new LogicException(sprintf('The injected serializer must be an instance of "%s".', NormalizerInterface::class));
            }

            $relatedContext = $this->createOperationContext($context, $resourceClass);
            $normalizedRelatedObject = $this->serializer->normalize($relatedObject, $format, $relatedContext);
            if (!\is_string($normalizedRelatedObject) && !\is_array($normalizedRelatedObject) && !$normalizedRelatedObject instanceof \ArrayObject && null !== $normalizedRelatedObject) {
                throw new UnexpectedValueException('Expected normalized relation to be an IRI, array, \ArrayObject or null');
            }

            return $normalizedRelatedObject;
        }

        // The line below has been modified to pass the context to the IriConverter
        $iri = $this->iriConverter->getIriFromResource($relatedObject, UrlGeneratorInterface::ABS_PATH, null, $context);

        if (isset($context['resources'])) {
            $context['resources'][$iri] = $iri;
        }

        $push = $propertyMetadata->getPush() ?? false;
        if (isset($context['resources_to_push']) && $push) {
            $context['resources_to_push'][$iri] = $iri;
        }

        return $iri;
    }
}
